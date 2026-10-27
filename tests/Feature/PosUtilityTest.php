<?php

namespace Tests\Feature;

use App\Models\PosNotification;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;
use App\Services\StockService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PosUtilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_and_stock_changes_create_scoped_notifications(): void
    {
        $this->seed(DatabaseSeeder::class);
        $cashier = User::where('email', 'dewi@pos-jagat.test')->firstOrFail();
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $other = User::where('email', 'bagas@pos-jagat.test')->firstOrFail();
        $product = Product::where('sku', 'DEMO-001')->firstOrFail();

        $checkout = [
            'idempotency_key' => (string) Str::uuid(), 'items' => [['product_id' => $product->id, 'qty' => 1]],
            'payment_method' => 'cash', 'amount_received' => 10000,
        ];
        $transaction = app(CheckoutService::class)->checkout($cashier, $checkout);
        $this->assertDatabaseHas('pos_notifications', ['user_id' => $cashier->id, 'type' => 'sale', 'url' => route('pos.transactions.show', $transaction)]);
        $this->assertDatabaseHas('pos_notifications', ['user_id' => $admin->id, 'type' => 'sale', 'url' => route('pos.transactions.show', $transaction)]);
        $this->assertDatabaseMissing('pos_notifications', ['user_id' => $other->id, 'type' => 'sale', 'url' => route('pos.transactions.show', $transaction)]);
        app(CheckoutService::class)->checkout($cashier, $checkout);
        $this->assertSame(2, PosNotification::where('url', route('pos.transactions.show', $transaction))->count());

        app(StockService::class)->adjust($product, $admin, 'in', 3, 'Pengujian stok');
        $this->assertDatabaseHas('pos_notifications', ['user_id' => $admin->id, 'type' => 'stock']);
        $this->assertDatabaseMissing('pos_notifications', ['user_id' => $cashier->id, 'type' => 'stock']);
    }

    public function test_notifications_and_global_search_respect_ownership(): void
    {
        $this->seed(DatabaseSeeder::class);
        $cashier = User::where('email', 'dewi@pos-jagat.test')->firstOrFail();
        $other = User::where('email', 'bagas@pos-jagat.test')->firstOrFail();
        $notification = PosNotification::where('user_id', $other->id)->firstOrFail();
        $transaction = PosTransaction::where('cashier_id', $other->id)->firstOrFail();

        $this->actingAs($cashier)->get(route('pos.notifications.feed'))
            ->assertOk()->assertJsonMissing(['read_url' => route('pos.notifications.read', $notification)]);

        $this->get(route('pos.search', ['q' => $transaction->transaction_number]))
            ->assertOk()->assertDontSee(route('pos.transactions.show', $transaction));
        $this->post(route('pos.notifications.read', $notification))->assertForbidden();
        $this->delete(route('pos.notifications.destroy', $notification))->assertForbidden();
        $this->assertDatabaseHas('pos_notifications', ['id' => $notification->id, 'read_at' => null]);

        $own = PosNotification::where('user_id', $cashier->id)->firstOrFail();
        $this->post(route('pos.notifications.read', $own))->assertRedirect($own->url);
        $this->assertNotNull($own->fresh()->read_at);
        $this->delete(route('pos.notifications.destroy', $own))->assertRedirect();
        $this->assertDatabaseMissing('pos_notifications', ['id' => $own->id]);
    }

    public function test_notification_bulk_actions_only_affect_current_user(): void
    {
        $this->seed(DatabaseSeeder::class);
        $cashier = User::where('email', 'dewi@pos-jagat.test')->firstOrFail();
        $other = User::where('email', 'bagas@pos-jagat.test')->firstOrFail();
        $own = PosNotification::where('user_id', $cashier->id)->firstOrFail();
        $theirs = PosNotification::where('user_id', $other->id)->firstOrFail();

        $this->actingAs($cashier)->post(route('pos.notifications.mark', $theirs))->assertForbidden();
        $this->post(route('pos.notifications.mark', $own))->assertRedirect();
        $this->assertNotNull($own->fresh()->read_at);

        PosNotification::where('user_id', $cashier->id)->update(['read_at' => null]);
        $this->post(route('pos.notifications.read-all'))->assertRedirect();
        $this->assertSame(0, PosNotification::where('user_id', $cashier->id)->whereNull('read_at')->count());
        $this->assertNull($theirs->fresh()->read_at);

        $this->delete(route('pos.notifications.destroy-all'))->assertRedirect();
        $this->assertSame(0, PosNotification::where('user_id', $cashier->id)->count());
        $this->assertDatabaseHas('pos_notifications', ['id' => $theirs->id]);
    }
}
