<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StoreSetting;
use App\Models\TransactionDraft;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class PosFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function product(int $stock = 5): Product
    {
        $category = Category::create(['name' => 'Minuman', 'is_active' => true]);
        $unit = Unit::create(['name' => 'Botol', 'symbol' => 'btl', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id, 'unit_id' => $unit->id, 'sku' => 'MIN-1',
            'name' => 'Air mineral', 'sell_price' => 10000, 'min_stock' => 2, 'is_active' => true,
        ]);
        $product->stock_qty = $stock;
        $product->save();

        return $product;
    }

    public function test_private_routes_and_role_scope(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $cashier = User::factory()->create(['role' => 'cashier']);
        $this->actingAs($cashier)->get(route('pos.manage.products.index'))->assertForbidden();
        $this->actingAs($cashier)->get(route('pos.cashier'))->assertOk();
    }

    public function test_barcode_lookup_uses_exact_active_product_and_current_stock(): void
    {
        $product = $this->product(0);
        $product->update(['barcode' => '8997000000012']);
        $this->getJson(route('pos.cashier.barcode', ['barcode' => $product->barcode]))->assertUnauthorized();

        $cashier = User::factory()->create(['role' => 'cashier']);
        $this->actingAs($cashier)->getJson(route('pos.cashier.barcode', ['barcode' => $product->barcode]))
            ->assertOk()
            ->assertJsonPath('id', $product->id)
            ->assertJsonPath('price', 10000)
            ->assertJsonPath('stock', 0);
        $this->getJson(route('pos.cashier.barcode', ['barcode' => '899700000001']))->assertNotFound();
        $this->getJson(route('pos.cashier.barcode'))->assertUnprocessable();

        $product->update(['is_active' => false]);
        $this->getJson(route('pos.cashier.barcode', ['barcode' => $product->barcode]))->assertNotFound();
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->create(['role' => 'cashier', 'is_active' => false, 'password' => 'long-test-password']);
        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'long-test-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_checkout_snapshots_and_idempotency(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        StoreSetting::current()->update(['tax_rate' => 10]);
        $payload = [
            'idempotency_key' => (string) Str::uuid(), 'items' => [['product_id' => $product->id, 'qty' => 2]],
            'discount_amount' => 1000, 'payment_method' => 'cash', 'amount_received' => 25000,
        ];
        $this->actingAs($cashier)->post(route('pos.checkout'), $payload)->assertRedirect();
        $transaction = PosTransaction::query()->sole();
        $this->assertSame(19000, $transaction->subtotal - $transaction->discount_amount);
        $this->assertSame(1900, $transaction->tax_amount);
        $this->assertSame(20900, $transaction->grand_total);
        $this->assertSame(3, $product->fresh()->stock_qty);
        $this->assertSame('Air mineral', $transaction->items()->first()->product_name);
        $this->assertSame(1, StockMovement::count());
        $this->actingAs($cashier)->post(route('pos.checkout'), $payload)->assertRedirect();
        $this->assertSame(1, PosTransaction::count());
        $this->assertSame(3, $product->fresh()->stock_qty);
    }

    public function test_cash_checkout_shows_success_toast_after_json_redirect(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $response = $this->actingAs($cashier)->postJson(route('pos.checkout'), [
            'idempotency_key' => (string) Str::uuid(),
            'items' => [['product_id' => $product->id, 'qty' => 1]],
            'payment_method' => 'cash',
            'amount_received' => 10000,
        ]);

        $response->assertOk()->assertJsonPath('status', 'completed');
        $this->get($response->json('transaction_url'))
            ->assertOk()
            ->assertSee('data-flash-status="Transaksi berhasil diselesaikan."', false);
    }

    public function test_checkout_rejects_insufficient_stock_and_payment(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product(1);
        $payload = [
            'idempotency_key' => (string) Str::uuid(), 'items' => [['product_id' => $product->id, 'qty' => 2]],
            'payment_method' => 'cash', 'amount_received' => 20000,
        ];
        $this->actingAs($cashier)->post(route('pos.checkout'), $payload)->assertSessionHasErrors('items');
        $payload['items'][0]['qty'] = 1;
        $payload['amount_received'] = 100;
        $this->actingAs($cashier)->post(route('pos.checkout'), $payload)->assertSessionHasErrors('amount_received');
        $this->assertSame(1, $product->fresh()->stock_qty);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_draft_does_not_reserve_stock_and_owner_is_enforced(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $other = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $this->actingAs($cashier)->post(route('pos.drafts.store'), ['items' => [['product_id' => $product->id, 'qty' => 2]]])->assertRedirect()->assertSessionHasNoErrors();
        $draft = TransactionDraft::query()->sole();
        $this->assertSame(5, $product->fresh()->stock_qty);
        $this->actingAs($other)->get(route('pos.drafts.show', $draft))->assertForbidden();
        $this->actingAs($other)->get(route('pos.drafts.index'))->assertDontSee('Draft #'.$draft->id);
    }

    public function test_stock_adjustment_tracks_movement_and_prevents_negative(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->product(2);
        $this->actingAs($admin)->post(route('pos.manage.stock.adjust', $product), ['type' => 'out', 'quantity' => 3, 'reason' => 'Rusak'])->assertSessionHasErrors('quantity');
        $this->actingAs($admin)->post(route('pos.manage.stock.adjust', $product), ['type' => 'out', 'quantity' => 1, 'reason' => 'Rusak'])->assertRedirect();
        $this->assertSame(1, $product->fresh()->stock_qty);
        $this->assertSame(-1, StockMovement::query()->sole()->delta_qty);
    }

    public function test_admin_pages_render_with_real_components(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->product();
        $this->actingAs($admin);
        foreach ([
            route('pos.manage.categories.index'), route('pos.manage.categories.create'),
            route('pos.manage.units.index'), route('pos.manage.units.create'),
            route('pos.manage.products.index'), route('pos.manage.products.create'),
            route('pos.manage.products.edit', $product), route('pos.manage.stock.index'),
            route('pos.manage.stock.movements'), route('pos.manage.users.index'),
            route('pos.manage.users.create'), route('pos.manage.settings.edit'),
            route('pos.manage.reports.show', 'sales'), route('pos.manage.reports.show', 'products'),
            route('pos.manage.reports.show', 'stock'), route('pos.manage.reports.show', 'drafts'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_product_table_sorts_across_paginated_data(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('pos.manage.products.index', ['sort' => 'stock_qty', 'direction' => 'desc']));
        $response->assertOk()->assertSee('data-pos-server-table', false)->assertSee('data-pos-live-filter', false);
        $products = $response->viewData('products');
        $this->assertGreaterThan($products->perPage(), $products->total());
        $this->assertSame($products->items()[0]->stock_qty, Product::max('stock_qty'));

        $this->get(route('pos.manage.products.index', ['sort' => 'stock_qty', 'direction' => 'invalid']))
            ->assertSessionHasErrors('direction');
    }

    public function test_draft_price_change_requires_acknowledgement_and_expiry_blocks_checkout(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product();
        $this->actingAs($cashier)->post(route('pos.drafts.store'), ['items' => [['product_id' => $product->id, 'qty' => 1]]])->assertSessionHasNoErrors();
        $draft = TransactionDraft::query()->sole();
        $product->update(['sell_price' => 12000]);
        $this->actingAs($cashier)->get(route('pos.drafts.show', $draft))->assertOk()->assertSee('Perubahan produk');
        $payload = [
            'idempotency_key' => (string) Str::uuid(), 'draft_id' => $draft->id,
            'items' => [['product_id' => $product->id, 'qty' => 1]],
            'payment_method' => 'cash', 'amount_received' => 12000,
        ];
        $this->actingAs($cashier)->post(route('pos.checkout'), $payload)->assertSessionHasErrors('draft');
        $this->actingAs($cashier)->post(route('pos.checkout'), $payload + ['acknowledge_changes' => true])->assertRedirect();
        $this->assertSame('completed', $draft->fresh()->status);
        $this->assertSame(12000, PosTransaction::query()->sole()->grand_total);

        $expired = TransactionDraft::create([
            'cashier_id' => $cashier->id, 'status' => 'active', 'expires_at' => now()->subMinute(),
            'payload_json' => ['items' => [['product_id' => $product->id, 'qty' => 1, 'unit_price' => 12000]]],
        ]);
        $payload['idempotency_key'] = (string) Str::uuid();
        $payload['draft_id'] = $expired->id;
        $this->actingAs($cashier)->post(route('pos.checkout'), $payload)->assertSessionHasErrors('draft');
    }

    public function test_draft_can_be_edited_with_new_items_from_cashier_page(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $first = $this->product();
        $second = Product::create([
            'category_id' => $first->category_id, 'unit_id' => $first->unit_id,
            'sku' => 'MIN-2', 'name' => 'Air soda', 'sell_price' => 12000,
            'min_stock' => 0, 'is_active' => true,
        ]);
        $second->update(['stock_qty' => 4]);
        $this->actingAs($cashier)->post(route('pos.drafts.store'), ['items' => [['product_id' => $first->id, 'qty' => 1]]])->assertSessionHasNoErrors();
        $draft = TransactionDraft::query()->sole();
        $this->actingAs($cashier)->get(route('pos.cashier', ['draft' => $draft->id]))->assertOk()->assertSee('Lanjutkan draft');
        $this->actingAs($cashier)->put(route('pos.drafts.update', $draft), [
            'items' => [['product_id' => $first->id, 'qty' => 2], ['product_id' => $second->id, 'qty' => 1]],
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertCount(2, $draft->fresh()->payload_json['items']);
        $this->assertSame(5, $first->fresh()->stock_qty);
    }

    public function test_reports_export_csv_and_admin_account_safety(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->product();
        $product->update(['name' => '=FORMULA']);
        $this->actingAs($admin)->post(route('pos.checkout'), [
            'idempotency_key' => (string) Str::uuid(), 'items' => [['product_id' => $product->id, 'qty' => 1]],
            'payment_method' => 'transfer', 'amount_received' => 10000,
        ])->assertRedirect();
        $this->actingAs($admin)->get(route('pos.manage.reports.show', 'sales'))->assertOk()->assertSee('10.000');
        $export = $this->actingAs($admin)->get(route('pos.manage.reports.export', 'products'));
        $export->assertOk();
        $this->assertStringContainsString("'=FORMULA", $export->streamedContent());
        $this->assertStringContainsString('10000', $this->actingAs($admin)->get(route('pos.manage.reports.export', 'sales'))->streamedContent());
        $this->actingAs($admin)->put(route('pos.manage.users.update', $admin), [
            'name' => $admin->name, 'email' => $admin->email, 'role' => 'cashier', 'is_active' => 1,
        ])->assertSessionHasErrors('role');
        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_checkout_failure_rolls_back_every_write(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product(2);
        StockMovement::creating(static function (): void {
            throw new RuntimeException('Simulated movement failure');
        });

        try {
            $this->actingAs($cashier)->post(route('pos.checkout'), [
                'idempotency_key' => (string) Str::uuid(), 'items' => [['product_id' => $product->id, 'qty' => 1]],
                'payment_method' => 'cash', 'amount_received' => 10000,
            ])->assertStatus(500);
        } finally {
            StockMovement::flushEventListeners();
        }

        $this->assertSame(2, $product->fresh()->stock_qty);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('transaction_items', 0);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_sold_product_is_hidden_after_delete_and_history_remains(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->product();
        $product->update(['barcode' => '8888166321106']);
        $this->actingAs($admin)->post(route('pos.checkout'), [
            'idempotency_key' => (string) Str::uuid(), 'items' => [['product_id' => $product->id, 'qty' => 1]],
            'payment_method' => 'cash', 'amount_received' => 10000,
        ])->assertRedirect();
        $product->update(['name' => 'Nama baru', 'sell_price' => 15000]);
        $this->actingAs($admin)->delete(route('pos.manage.products.destroy', $product))->assertRedirect();
        $this->assertNull(Product::find($product->id));
        $this->assertNotNull(Product::withTrashed()->findOrFail($product->id)->deleted_at);
        $this->assertNull(Product::withTrashed()->findOrFail($product->id)->barcode);
        $this->get(route('pos.manage.products.index'))->assertDontSee('Nama baru');
        $this->get(route('pos.cashier'))->assertDontSee('Nama baru');
        $this->get(route('pos.manage.stock.movements'))->assertOk()->assertSee('Nama baru');
        $this->get(route('pos.manage.products.edit', $product))->assertNotFound();
        $item = PosTransaction::query()->sole()->items()->sole();
        $this->assertSame('Air mineral', $item->product_name);
        $this->assertSame(10000, $item->unit_price);
        $this->actingAs($admin)->post(route('pos.manage.products.store'), [
            'category_id' => $product->category_id, 'unit_id' => $product->unit_id,
            'name' => 'Produk pengganti', 'barcode' => '8888166321106',
            'sell_price' => 15000, 'min_stock' => 0, 'initial_stock' => 0, 'is_active' => 1,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('Produk pengganti', Product::where('barcode', '8888166321106')->sole()->name);
    }

    public function test_last_unit_of_stock_can_only_be_sold_once(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $product = $this->product(1);
        foreach ([0, 1] as $attempt) {
            $response = $this->actingAs($cashier)->post(route('pos.checkout'), [
                'idempotency_key' => (string) Str::uuid(), 'items' => [['product_id' => $product->id, 'qty' => 1]],
                'payment_method' => 'cash', 'amount_received' => 10000,
            ]);
            $attempt === 0 ? $response->assertRedirect() : $response->assertSessionHasErrors('items');
        }
        $this->assertSame(0, $product->fresh()->stock_qty);
        $this->assertSame(1, PosTransaction::count());
    }

    public function test_master_data_validation_and_stock_opening_movement(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('pos.manage.categories.store'), ['name' => 'Minuman', 'is_active' => 1])->assertRedirect();
        $this->actingAs($admin)->post(route('pos.manage.units.store'), ['name' => 'Botol', 'symbol' => 'btl', 'is_active' => 1])->assertRedirect();
        $this->actingAs($admin)->post(route('pos.manage.categories.store'), ['name' => 'Minuman', 'is_active' => 1])->assertSessionHasErrors('name');
        $this->actingAs($admin)->post(route('pos.manage.products.store'), [
            'category_id' => Category::query()->sole()->id, 'unit_id' => Unit::query()->sole()->id,
            'sku' => 'ABC-1', 'name' => 'Air mineral', 'sell_price' => 10000,
            'min_stock' => 2, 'initial_stock' => 8, 'is_active' => 1,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $product = Product::query()->sole();
        $this->assertMatchesRegularExpression('/^PRD-[A-Z0-9]{12}$/', $product->sku);
        $this->assertNotSame('ABC-1', $product->sku);
        $this->assertSame(8, $product->stock_qty);
        $this->assertSame(8, StockMovement::query()->sole()->delta_qty);
        $this->actingAs($admin)->delete(route('pos.manage.categories.destroy', $product->category))->assertSessionHasErrors('delete');
    }

    public function test_sku_is_generated_once_and_product_without_history_is_removed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Camilan', 'is_active' => true]);
        $unit = Unit::create(['name' => 'Bungkus', 'symbol' => 'bks', 'is_active' => true]);
        $data = [
            'category_id' => $category->id, 'unit_id' => $unit->id,
            'name' => 'Keripik', 'sell_price' => 5000, 'min_stock' => 0,
            'initial_stock' => 0, 'is_active' => 1,
        ];

        $this->actingAs($admin)->post(route('pos.manage.products.store'), $data + ['sku' => 'PAKSA-1'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->post(route('pos.manage.products.store'), $data)->assertRedirect()->assertSessionHasNoErrors();
        $products = Product::query()->orderBy('id')->get();
        $this->assertCount(2, $products);
        $this->assertNotSame($products[0]->sku, $products[1]->sku);
        $this->assertMatchesRegularExpression('/^PRD-[A-Z0-9]{12}$/', $products[0]->sku);

        $this->put(route('pos.manage.products.update', $products[0]), array_diff_key($data, ['initial_stock' => true]) + ['sku' => 'PAKSA-2'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame($products[0]->sku, $products[0]->fresh()->sku);

        $this->delete(route('pos.manage.products.destroy', $products[0]))->assertRedirect();
        $this->assertNull(Product::withTrashed()->find($products[0]->id));
        $this->assertSame(1, Product::count());
    }

    public function test_optional_product_image_can_be_uploaded_replaced_and_removed(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->product();
        $data = [
            'category_id' => $product->category_id, 'unit_id' => $product->unit_id,
            'sku' => $product->sku, 'name' => $product->name, 'sell_price' => $product->sell_price,
            'min_stock' => $product->min_stock, 'is_active' => 1,
        ];

        $this->actingAs($admin)->put(route('pos.manage.products.update', $product), $data + ['image' => UploadedFile::fake()->image('satu.jpg')])
            ->assertRedirect()->assertSessionHasNoErrors();
        $firstPath = $product->fresh()->image_path;
        $this->assertNotNull($firstPath);
        Storage::disk('public')->assertExists($firstPath);
        $this->get(route('pos.cashier'))->assertOk()->assertSee('storage/'.$firstPath)->assertSee('Tambah ke keranjang');

        $this->put(route('pos.manage.products.update', $product), $data + ['image' => UploadedFile::fake()->image('dua.png')])
            ->assertRedirect()->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($firstPath);
        $secondPath = $product->fresh()->image_path;
        Storage::disk('public')->assertExists($secondPath);

        $this->put(route('pos.manage.products.update', $product), $data + ['image' => UploadedFile::fake()->create('bukan-gambar.pdf', 10, 'application/pdf')])
            ->assertSessionHasErrors('image');
        $this->assertSame($secondPath, $product->fresh()->image_path);

        $this->put(route('pos.manage.products.update', $product), $data + ['remove_image' => 1])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertNull($product->fresh()->image_path);
        Storage::disk('public')->assertMissing($secondPath);

        $this->post(route('pos.manage.products.store'), array_replace($data, ['sku' => 'GAMBAR-1', 'initial_stock' => 0, 'image' => UploadedFile::fake()->image('baru.jpg')]))
            ->assertRedirect()->assertSessionHasNoErrors();
        $newProduct = Product::query()->where('name', $product->name)->where('id', '!=', $product->id)->sole();
        $this->assertMatchesRegularExpression('/^PRD-[A-Z0-9]{12}$/', $newProduct->sku);
        Storage::disk('public')->assertExists($newProduct->image_path);
    }

    public function test_setting_changes_do_not_rewrite_transaction_tax_snapshot(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->product();
        StoreSetting::current()->update(['tax_rate' => 10]);
        $this->actingAs($admin)->post(route('pos.checkout'), [
            'idempotency_key' => (string) Str::uuid(), 'items' => [['product_id' => $product->id, 'qty' => 1]],
            'payment_method' => 'cash', 'amount_received' => 11000,
        ])->assertRedirect();
        StoreSetting::current()->update(['tax_rate' => 0]);
        $transaction = PosTransaction::query()->sole();
        $this->assertSame(1000, $transaction->tax_amount);
        $this->assertSame('10.00', $transaction->tax_rate);
        $this->actingAs($admin)->get(route('pos.transactions.receipt', $transaction))->assertOk()->assertSee('11.000');
    }
}
