<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PosNotification;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\TransactionDraft;
use App\Models\Unit;
use App\Models\User;
use App\Services\MidtransGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use RefreshDatabase;

    private function product(): Product
    {
        $category = Category::create(['name' => 'Minuman', 'is_active' => true]);
        $unit = Unit::create(['name' => 'Botol', 'symbol' => 'btl', 'is_active' => true]);
        $product = Product::create(['category_id' => $category->id, 'unit_id' => $unit->id, 'sku' => 'AIR-1', 'name' => 'Air mineral', 'sell_price' => 10000, 'is_active' => true]);
        $product->stock_qty = 5;
        $product->save();

        return $product;
    }

    private function payload(Product $product, ?TransactionDraft $draft = null): array
    {
        return array_filter([
            'idempotency_key' => (string) Str::uuid(),
            'items' => [['product_id' => $product->id, 'qty' => 2]],
            'payment_method' => 'midtrans',
            'draft_id' => $draft?->id,
        ], fn ($value) => $value !== null);
    }

    private function gateway(): MidtransGateway
    {
        config(['services.midtrans.server_key' => 'sandbox-test-key', 'services.midtrans.client_key' => 'sandbox-client-key']);
        $gateway = Mockery::mock(MidtransGateway::class);
        $gateway->shouldReceive('configured')->andReturnTrue();
        $gateway->shouldReceive('create')->once()->andReturn(['token' => 'snap-token-example', 'url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/example']);
        $this->app->instance(MidtransGateway::class, $gateway);

        return $gateway;
    }

    private function midtransStatus(PosTransaction $transaction, string $remote): array
    {
        return ['order_id' => $transaction->transaction_number, 'status_code' => '200', 'gross_amount' => '20000.00', 'transaction_status' => $remote];
    }

    public function test_failed_initiation_can_be_recovered_and_cancelled_once(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        config(['services.midtrans.server_key' => 'sandbox-test-key', 'services.midtrans.client_key' => 'sandbox-client-key']);
        $gateway = Mockery::mock(MidtransGateway::class);
        $gateway->shouldReceive('configured')->andReturnTrue();
        $gateway->shouldReceive('create')->once()->andThrow(new \RuntimeException('Connection timeout'));
        $gateway->shouldReceive('create')->once()->andReturn(['token' => 'recovered-token', 'url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/recovered']);
        $gateway->shouldReceive('status')->twice()->andReturnNull();
        $gateway->shouldReceive('cancelSession')->once()->andReturnTrue();
        $this->app->instance(MidtransGateway::class, $gateway);
        $this->actingAs($cashier)->postJson(route('pos.checkout'), $this->payload($product))->assertOk();
        $transaction = PosTransaction::query()->sole();
        $this->assertSame(3, $product->fresh()->stock_qty);
        $this->post(route('pos.transactions.cancel-payment', $transaction))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('failed', $transaction->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock_qty);
        $this->post(route('pos.transactions.cancel-payment', $transaction))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame(5, $product->fresh()->stock_qty);
        $this->assertSame(1, StockMovement::where('type', 'release')->count());
    }

    public function test_pending_payment_is_only_restocked_after_remote_cancellation(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $gateway = $this->gateway();
        $this->actingAs($cashier)->postJson(route('pos.checkout'), $this->payload($product))->assertOk();
        $transaction = PosTransaction::query()->sole();
        $gateway->shouldReceive('status')->once()->andReturn($this->midtransStatus($transaction, 'pending'));
        $gateway->shouldReceive('cancel')->once();
        $gateway->shouldReceive('status')->once()->andReturn($this->midtransStatus($transaction, 'cancel'));
        $this->post(route('pos.transactions.cancel-payment', $transaction))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('failed', $transaction->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock_qty);
    }

    public function test_uncertain_cancellation_keeps_stock_reserved_and_other_cashiers_are_forbidden(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $gateway = $this->gateway();
        $this->actingAs($cashier)->postJson(route('pos.checkout'), $this->payload($product))->assertOk();
        $transaction = PosTransaction::query()->sole();
        $this->actingAs(User::factory()->create(['role' => 'cashier']))->post(route('pos.transactions.cancel-payment', $transaction))->assertForbidden();
        $gateway->shouldReceive('status')->twice()->andReturn($this->midtransStatus($transaction, 'pending'));
        $gateway->shouldReceive('cancel')->once();
        $this->actingAs($cashier)->post(route('pos.transactions.cancel-payment', $transaction))->assertRedirect()->assertSessionHasErrors('payment');
        $this->assertSame('pending', $transaction->fresh()->status);
        $this->assertSame(3, $product->fresh()->stock_qty);
        $this->assertSame(0, StockMovement::where('type', 'release')->count());
    }

    public function test_a_payment_settling_during_session_cancellation_is_not_restocked(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $gateway = $this->gateway();
        $this->actingAs($cashier)->postJson(route('pos.checkout'), $this->payload($product))->assertOk();
        $transaction = PosTransaction::query()->sole();
        $gateway->shouldReceive('status')->once()->andReturnNull();
        $gateway->shouldReceive('cancelSession')->once()->andReturnFalse();
        $gateway->shouldReceive('status')->once()->andReturn($this->midtransStatus($transaction, 'settlement'));
        $this->post(route('pos.transactions.cancel-payment', $transaction))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('completed', $transaction->fresh()->status);
        $this->assertSame(3, $product->fresh()->stock_qty);
        $this->assertSame(0, StockMovement::where('type', 'release')->count());
    }

    public function test_snap_cancellation_checks_provider_confirmation_and_environment(): void
    {
        config(['services.midtrans.server_key' => 'sandbox-test-key', 'services.midtrans.is_production' => false]);
        Http::preventStrayRequests();
        Http::fake(['app.sandbox.midtrans.com/*' => Http::sequence()
            ->push(['canceled_at' => '2026-10-29T13:00:00Z'])
            ->push(['error_messages' => ['token not found']], 404)]);
        $transaction = new PosTransaction(['midtrans_snap_token' => 'snap-test-token']);
        $this->assertTrue(app(MidtransGateway::class)->cancelSession($transaction));
        Http::assertSent(fn ($request) => $request->url() === 'https://app.sandbox.midtrans.com/snap/v1/transactions/snap-test-token/cancel'
            && $request->hasHeader('Authorization', 'sandbox-test-key'));
        $this->expectException(\RuntimeException::class);
        app(MidtransGateway::class)->cancelSession($transaction);
    }

    public function test_pending_payment_completes_once_after_manual_check(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $gateway = $this->gateway();
        $payload = $this->payload($product);

        $this->actingAs($cashier)->post(route('pos.checkout'), $payload)->assertRedirect()->assertSessionHasNoErrors();
        $transaction = PosTransaction::query()->sole();
        $this->assertSame('pending', $transaction->status);
        $this->assertSame('snap-token-example', $transaction->midtrans_snap_token);
        $this->assertNull($transaction->completed_at);
        $this->assertSame(3, $product->fresh()->stock_qty);
        $this->assertDatabaseCount('pos_notifications', 0);
        $this->actingAs($cashier)->get(route('pos.transactions.show', $transaction))->assertOk()->assertSee('Cek pembayaran');
        $this->actingAs($cashier)->get(route('pos.transactions.receipt', $transaction))->assertNotFound();

        $gateway->shouldReceive('status')->once()->andReturnNull();
        $pending = $this->midtransStatus($transaction, 'pending');
        $pending['status_code'] = '201';
        $gateway->shouldReceive('status')->once()->andReturn($pending);
        $gateway->shouldReceive('status')->once()->andReturn($this->midtransStatus($transaction, 'settlement'));
        $this->actingAs($cashier)->post(route('pos.transactions.check-payment', $transaction))->assertRedirect();
        $this->assertSame('pending', $transaction->fresh()->status);
        $this->actingAs($cashier)->post(route('pos.transactions.check-payment', $transaction))->assertRedirect();
        $this->assertSame('pending', $transaction->fresh()->status);
        $this->actingAs($cashier)->post(route('pos.transactions.check-payment', $transaction))->assertRedirect();
        $this->assertSame('completed', $transaction->fresh()->status);
        $this->assertSame(20000, $transaction->fresh()->amount_received);
        $this->assertSame(3, $product->fresh()->stock_qty);
        $this->actingAs($cashier)->get(route('pos.transactions.receipt', $transaction))->assertOk();
        $this->actingAs($cashier)->post(route('pos.transactions.check-payment', $transaction))->assertRedirect();
        $this->assertSame(1, PosNotification::where('type', 'sale')->count());
        $this->assertSame(1, StockMovement::count());
    }

    public function test_failed_payment_restores_stock_and_draft_once(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $draft = TransactionDraft::create(['cashier_id' => $cashier->id, 'status' => 'active', 'expires_at' => now()->addHour(), 'payload_json' => ['items' => [['product_id' => $product->id, 'qty' => 2, 'unit_price' => 10000, 'product_name' => $product->name]], 'discount_amount' => 0]]);
        $gateway = $this->gateway();

        $this->actingAs($cashier)->post(route('pos.checkout'), $this->payload($product, $draft))->assertRedirect()->assertSessionHasNoErrors();
        $transaction = PosTransaction::query()->sole();
        $this->assertSame($draft->id, $transaction->draft_id);
        $this->assertSame('pending_payment', $draft->fresh()->status);
        $this->actingAs($cashier)->delete(route('pos.drafts.destroy', $draft))->assertSessionHasErrors('draft');

        $expired = $this->midtransStatus($transaction, 'expire');
        $expired['status_code'] = '407';
        $gateway->shouldReceive('status')->once()->andReturn($expired);
        $this->actingAs($cashier)->post(route('pos.transactions.check-payment', $transaction))->assertRedirect();
        $this->assertSame('failed', $transaction->fresh()->status);
        $this->assertSame('active', $draft->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock_qty);
        $this->assertSame(1, StockMovement::where('type', 'release')->count());
        $this->actingAs($cashier)->post(route('pos.transactions.check-payment', $transaction))->assertRedirect();
        $this->assertSame(5, $product->fresh()->stock_qty);
        $this->actingAs($cashier)->get(route('pos.transactions.receipt', $transaction))->assertNotFound();
    }

    public function test_failed_payment_restores_stock_for_deleted_product(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->product();
        $gateway = $this->gateway();

        $this->actingAs($cashier)->post(route('pos.checkout'), $this->payload($product))
            ->assertRedirect()->assertSessionHasNoErrors();
        $transaction = PosTransaction::query()->sole();
        $this->actingAs($admin)->delete(route('pos.manage.products.destroy', $product))->assertRedirect();
        $this->assertNull(Product::find($product->id));

        $expired = $this->midtransStatus($transaction, 'expire');
        $expired['status_code'] = '407';
        $gateway->shouldReceive('status')->once()->andReturn($expired);
        $this->actingAs($cashier)->post(route('pos.transactions.check-payment', $transaction))
            ->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame('failed', $transaction->fresh()->status);
        $this->assertSame(5, Product::withTrashed()->findOrFail($product->id)->stock_qty);
        $this->assertSame(1, StockMovement::where('type', 'release')->count());
    }

    public function test_mismatched_status_and_other_cashier_cannot_complete_payment(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $other = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $gateway = $this->gateway();
        $this->actingAs($cashier)->post(route('pos.checkout'), $this->payload($product))->assertRedirect();
        $transaction = PosTransaction::query()->sole();
        $this->actingAs($other)->post(route('pos.transactions.check-payment', $transaction))->assertForbidden();
        $invalid = $this->midtransStatus($transaction, 'settlement');
        $invalid['gross_amount'] = '10000.00';
        $gateway->shouldReceive('status')->once()->andReturn($invalid);
        $this->actingAs($cashier)->post(route('pos.transactions.check-payment', $transaction))->assertSessionHasErrors('payment');
        $this->assertSame('pending', $transaction->fresh()->status);
        $this->assertSame(3, $product->fresh()->stock_qty);
    }

    public function test_missing_server_key_prevents_midtrans_checkout(): void
    {
        config(['services.midtrans.server_key' => null]);
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $this->actingAs($cashier)->post(route('pos.checkout'), $this->payload($product))->assertSessionHasErrors('payment_method');
        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(5, $product->fresh()->stock_qty);
    }

    public function test_missing_client_key_prevents_popup_checkout(): void
    {
        config(['services.midtrans.server_key' => 'sandbox-test-key', 'services.midtrans.client_key' => null]);
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();

        $this->actingAs($cashier)->postJson(route('pos.checkout'), $this->payload($product))
            ->assertUnprocessable()->assertJsonValidationErrors('payment_method');
        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(5, $product->fresh()->stock_qty);
    }

    public function test_failed_session_creation_can_be_retried_without_releasing_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        config(['services.midtrans.server_key' => 'sandbox-test-key', 'services.midtrans.client_key' => 'sandbox-client-key']);
        $gateway = Mockery::mock(MidtransGateway::class);
        $gateway->shouldReceive('configured')->andReturnTrue();
        $gateway->shouldReceive('create')->once()->andThrow(new \RuntimeException('Midtrans unavailable'));
        $this->app->instance(MidtransGateway::class, $gateway);

        $this->actingAs($cashier)->post(route('pos.checkout'), $this->payload($product))->assertRedirect()->assertSessionHasErrors('payment');
        $transaction = PosTransaction::query()->sole();
        $this->assertSame('pending', $transaction->status);
        $this->assertSame('initiation_error', $transaction->midtrans_status);
        $this->assertSame(3, $product->fresh()->stock_qty);
        $this->actingAs($cashier)->get(route('pos.transactions.show', $transaction))->assertSee('Buat ulang sesi pembayaran');

        $gateway->shouldReceive('create')->once()->andReturn(['token' => 'retry-snap-token', 'url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/example']);
        $this->actingAs($cashier)->post(route('pos.transactions.retry-payment', $transaction))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertNotNull($transaction->fresh()->midtrans_redirect_url);
        $this->assertSame('retry-snap-token', $transaction->fresh()->midtrans_snap_token);
        $this->assertSame(3, $product->fresh()->stock_qty);
        $this->assertSame(1, StockMovement::count());
    }

    public function test_cashier_checkout_returns_snap_token_for_popup_and_checks_status_as_json(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $gateway = $this->gateway();

        $this->actingAs($cashier)->get(route('pos.cashier'))->assertOk()
            ->assertSee('data-modal-open="#cashier-payment"', false)
            ->assertSee('peer-checked:border-primary', false)
            ->assertSee('data-grouped-number', false)
            ->assertDontSee('<select name="payment_method"', false);
        $response = $this->actingAs($cashier)->postJson(route('pos.checkout'), $this->payload($product));
        $response->assertOk()->assertJsonPath('snap_token', 'snap-token-example')->assertJsonPath('status', 'pending');
        $transaction = PosTransaction::query()->sole();
        $this->actingAs($cashier)->get(route('pos.transactions.show', $transaction))->assertOk()->assertSee('data-snap-pay', false);

        $gateway->shouldReceive('status')->once()->andReturn($this->midtransStatus($transaction, 'settlement'));
        $this->actingAs($cashier)->postJson(route('pos.transactions.check-payment', $transaction))->assertOk()->assertJsonPath('status', 'completed');
        $this->get(route('pos.transactions.show', $transaction))->assertSee('data-flash-status="Pembayaran Midtrans berhasil dikonfirmasi."', false);
        $this->assertSame(3, $product->fresh()->stock_qty);
    }
}
