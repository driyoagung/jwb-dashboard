<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\Receivable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReceivableFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_credit_checkout_requires_customer_and_creates_one_receivable(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $this->seed();
        $product = Product::where('sku', 'DEMO-001')->firstOrFail();
        $customer = Customer::firstOrFail();
        $stock = $product->stock_qty;
        $payload = [
            'idempotency_key' => (string) Str::uuid(), 'items' => [['product_id' => $product->id, 'qty' => 1]],
            'payment_method' => 'credit', 'due_date' => now()->addDays(7)->toDateString(),
        ];
        $this->actingAs($cashier)->post(route('pos.checkout'), $payload)->assertSessionHasErrors('customer_id');
        $payload['customer_id'] = $customer->id;
        $this->post(route('pos.checkout'), $payload)->assertRedirect()->assertSessionHasNoErrors();
        $receivable = Receivable::latest('id')->firstOrFail();
        $this->assertSame($customer->id, $receivable->customer_id);
        $this->assertSame($receivable->total_amount, $receivable->balance);
        $this->assertSame('credit', $receivable->transaction->payment_method);
        $this->assertSame(0, $receivable->transaction->amount_received);
        $this->assertSame($stock - 1, $product->fresh()->stock_qty);
        $this->post(route('pos.checkout'), $payload)->assertRedirect();
        $this->assertDatabaseCount('receivables', 9);
    }

    public function test_partial_repayment_cannot_overpay_or_count_as_new_sale(): void
    {
        $this->seed();
        $cashier = User::where('role', 'cashier')->firstOrFail();
        $receivable = Receivable::where('status', 'open')->firstOrFail();
        $sales = PosTransaction::count();
        $this->actingAs($cashier)->get(route('pos.receivables.show', $receivable))->assertOk();
        $key = (string) Str::uuid();
        $this->post(route('pos.receivables.payments.store', $receivable), ['idempotency_key' => $key, 'amount' => 1000, 'method' => 'cash'])->assertRedirect();
        $this->assertSame(1000, $receivable->fresh()->paid_amount);
        $this->assertSame('partial', $receivable->fresh()->status);
        $this->post(route('pos.receivables.payments.store', $receivable), ['idempotency_key' => $key, 'amount' => 1000, 'method' => 'cash'])->assertRedirect();
        $this->assertSame(1, $receivable->payments()->count());
        $this->post(route('pos.receivables.payments.store', $receivable), ['idempotency_key' => (string) Str::uuid(), 'amount' => $receivable->total_amount, 'method' => 'cash'])->assertSessionHasErrors('amount');
        $this->assertSame($sales, PosTransaction::count());
        $this->post(route('pos.receivables.payments.store', $receivable), ['idempotency_key' => (string) Str::uuid(), 'amount' => $receivable->fresh()->balance, 'method' => 'transfer'])->assertRedirect();
        $this->assertSame('paid', $receivable->fresh()->status);
        $this->assertSame(0, $receivable->fresh()->balance);
    }

    public function test_logout_is_in_header_and_customer_can_be_created(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $this->actingAs($cashier)->get(route('pos.customers.index'))->assertOk()->assertSee('action="'.route('logout').'"', false);
        $this->post(route('pos.customers.store'), ['name' => 'Pelanggan Baru', 'phone' => '08123456789'])->assertRedirect();
        $this->assertDatabaseHas('customers', ['name' => 'Pelanggan Baru']);
        $this->post(route('pos.customers.store'), ['name' => 'Pelanggan Kasir', 'return_to' => 'cashier'])->assertRedirect();
        $customer = Customer::where('name', 'Pelanggan Kasir')->firstOrFail();
        $this->get(route('pos.cashier', ['customer_id' => $customer->id]))->assertOk()->assertSee('Pelanggan Kasir');
    }

    public function test_customer_credit_and_report_filters_render_live_results(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin);

        $this->get(route('pos.customers.index', ['q' => 'Budi', 'sort' => 'name', 'direction' => 'desc']))
            ->assertOk()->assertSee('Budi Santoso')->assertDontSee('Siti Rahma')
            ->assertSee('data-pos-live-filter', false)->assertSee('data-pos-results', false)->assertDontSee('Terapkan');
        $this->get(route('pos.receivables.index', ['status' => 'paid', 'sort' => 'balance', 'direction' => 'asc']))
            ->assertOk()->assertSee('data-pos-live-filter', false)->assertSee('data-pos-results', false)->assertDontSee('Terapkan');
        $this->get(route('pos.manage.reports.show', 'sales'))
            ->assertOk()->assertSee('data-pos-live-filter', false)->assertSee('data-pos-results', false)->assertDontSee('Terapkan');
    }
}
