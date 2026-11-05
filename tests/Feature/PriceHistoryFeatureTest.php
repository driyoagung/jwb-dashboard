<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PriceHistoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function productData(): array
    {
        return ['category_id' => Category::create(['name' => 'Makanan', 'is_active' => true])->id,
            'unit_id' => Unit::create(['name' => 'Pcs', 'symbol' => 'pcs', 'is_active' => true])->id,
            'name' => 'Produk audit', 'cost_price' => 6000, 'sell_price' => 10000,
            'min_stock' => 0, 'initial_stock' => 20, 'is_active' => true];
    }

    public function test_price_changes_are_atomic_attributed_and_only_recorded_when_changed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $data = $this->productData();
        $this->actingAs($admin)->post(route('pos.manage.products.store'), $data)->assertSessionHasNoErrors();
        $product = Product::sole();
        $this->assertDatabaseCount('price_histories', 2);
        unset($data['initial_stock']);
        $this->put(route('pos.manage.products.update', $product), $data)->assertSessionHasNoErrors();
        $this->assertDatabaseCount('price_histories', 2);
        $data['cost_price'] = 7000;
        $data['sell_price'] = 12000;
        $this->put(route('pos.manage.products.update', $product), $data)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('price_histories', ['product_id' => $product->id, 'user_id' => $admin->id, 'price_type' => 'cost', 'old_price' => 6000, 'new_price' => 7000]);
        $this->assertDatabaseHas('price_histories', ['price_type' => 'sell', 'old_price' => 10000, 'new_price' => 12000]);
        $this->put(route('pos.manage.products.update', $product), array_replace($data, ['cost_price' => 13000]))->assertSessionHasErrors('cost_price');
        $this->assertSame(7000, $product->fresh()->cost_price);
        $this->assertDatabaseCount('price_histories', 4);
        $sellOnly = array_diff_key($data, ['cost_price' => true]);
        $this->put(route('pos.manage.products.update', $product), array_replace($sellOnly, ['sell_price' => 6000]))->assertSessionHasErrors('cost_price');
        $this->assertSame(12000, $product->fresh()->sell_price);
        $this->get(route('pos.manage.price-histories.index', ['product_id' => $product->id, 'price_type' => 'cost', 'q' => 'audit']))->assertOk()->assertViewHas('histories', fn ($rows) => $rows->total() === 2);
        $this->delete(route('pos.manage.products.destroy', $product))->assertRedirect();
        $this->get(route('pos.manage.price-histories.index'))->assertOk()->assertSee('Dihapus dari katalog');
        $this->actingAs(User::factory()->create(['role' => 'cashier']))->get(route('pos.manage.price-histories.index'))->assertForbidden();
    }

    public function test_cost_cannot_exceed_sell_price_on_create_and_zero_or_empty_cost_is_allowed(): void
    {
        $data = $this->productData();
        $this->actingAs(User::factory()->create(['role' => 'admin']))->post(route('pos.manage.products.store'), array_replace($data, ['cost_price' => 11000]))->assertSessionHasErrors('cost_price');
        $this->assertDatabaseCount('products', 0);
        foreach ([0, null] as $cost) {
            $this->post(route('pos.manage.products.store'), array_replace($data, ['cost_price' => $cost]))->assertSessionHasNoErrors();
        }
        $this->assertDatabaseCount('products', 2);
    }

    public function test_profit_uses_checkout_cost_snapshot_discount_and_excludes_tax_and_unknown_costs(): void
    {
        StoreSetting::current()->update(['tax_rate' => 11]);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('pos.manage.products.store'), $this->productData())->assertSessionHasNoErrors();
        $product = Product::sole();
        $payload = ['items' => [['product_id' => $product->id, 'qty' => 2]], 'discount_amount' => 1000, 'payment_method' => 'cash', 'amount_received' => 50000, 'idempotency_key' => (string) Str::uuid()];
        $this->postJson(route('pos.checkout'), $payload)->assertOk();
        $sale = PosTransaction::sole();
        $this->assertSame(12000, $sale->cost_total);
        $this->assertSame(7000, $sale->gross_profit);
        $this->assertEquals(2090, $sale->tax_amount);
        $this->assertSame(6000, $sale->items->sole()->unit_cost);
        $product->update(['cost_price' => null]);
        $this->assertSame(7000, $sale->fresh()->gross_profit);
        $this->postJson(route('pos.checkout'), array_replace($payload, ['idempotency_key' => (string) Str::uuid()]))->assertOk();
        $this->assertNull(PosTransaction::latest('id')->first()->gross_profit);
        $this->get(route('pos.manage.reports.show', 'sales'))->assertOk()->assertSee('Rp 7.000')->assertSee('1 transaksi belum memiliki catatan modal');
        $export = $this->get(route('pos.manage.reports.export', 'sales'))->assertOk()->streamedContent();
        $this->assertStringContainsString('Laba kotor', $export);
        $this->assertStringContainsString('Modal belum tercatat', $export);
        $this->assertStringContainsString('12000,7000', $export);
    }

    public function test_client_edition_blocks_credit_and_customers_without_deleting_data_and_can_be_restored(): void
    {
        config(['pos.features.customers' => false, 'pos.features.credit' => false]);
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('pos.customers.index'))->assertForbidden();
        $this->get(route('pos.receivables.index'))->assertForbidden();
        $this->get(route('pos.cashier'))->assertOk()->assertDontSee('data-cart-credit-group', false)->assertDontSee('value="credit"', false)->assertDontSee('href="'.route('pos.customers.index').'"', false);
        $this->postJson(route('pos.checkout'), ['items' => [], 'payment_method' => 'credit', 'idempotency_key' => (string) Str::uuid()])->assertUnprocessable()->assertJsonValidationErrors('payment_method');
        config(['pos.features.customers' => true, 'pos.features.credit' => true]);
        $this->get(route('pos.customers.index'))->assertOk();
        $this->get(route('pos.receivables.index'))->assertOk();
        $this->get(route('pos.cashier'))->assertOk()->assertSee('data-cart-credit-group', false);
    }

    public function test_supported_images_are_served_without_a_storage_link_and_invalid_files_are_rejected(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $data = $this->productData();
        foreach (['jpg', 'png', 'webp', 'gif'] as $extension) {
            $this->actingAs($admin)->post(route('pos.manage.products.store'), $data + ['image' => UploadedFile::fake()->image('image.'.$extension)])->assertSessionHasNoErrors();
            $product = Product::latest('id')->first();
            $response = $this->get($product->image_url)->assertOk();
            $this->assertStringStartsWith('image/', $response->headers->get('Content-Type'));
            $this->assertNotEmpty($response->streamedContent());
        }
        $this->post(route('pos.manage.products.store'), $data + ['image' => UploadedFile::fake()->create('bad.svg', 1, 'image/svg+xml')])->assertSessionHasErrors('image');
        $this->post(route('pos.manage.products.store'), $data + ['image' => UploadedFile::fake()->image('large.jpg')->size(5121)])->assertSessionHasErrors('image');
        $this->assertDatabaseCount('products', 4);
        Storage::disk('public')->delete($product->image_path);
        $this->get($product->image_url)->assertNotFound();
        $this->get(route('pos.manage.products.edit', $product))->assertOk()->assertSee('Berkas gambar lama tidak ditemukan');
        $this->post(route('logout'))->assertRedirect();
        $this->get($product->image_url)->assertRedirect(route('login'));
    }
}
