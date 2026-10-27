<?php

namespace Tests\Feature;

use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\StockMovement;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_populates_related_pos_data_without_duplicating_it(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 5);
        $this->assertTrue(Hash::check('admin123', DB::table('users')->where('email', 'admin@gmail.com')->value('password')));
        $this->assertDatabaseCount('store_settings', 1);
        $this->assertDatabaseCount('categories', 6);
        $this->assertDatabaseCount('units', 4);
        $this->assertDatabaseCount('products', 32);
        $this->assertSame(32, Product::query()->whereNotNull('barcode')->distinct()->count('barcode'));
        $imagePaths = Product::query()->pluck('image_path')->unique();
        $this->assertCount(15, $imagePaths);
        foreach ($imagePaths as $path) {
            Storage::disk('public')->assertExists($path);
        }
        $this->assertDatabaseCount('transactions', 90);
        $this->assertDatabaseCount('customers', 8);
        $this->assertDatabaseCount('receivables', 8);
        $this->assertDatabaseCount('receivable_payments', 3);
        $this->assertGreaterThan(0, PosTransaction::query()->whereDate('completed_at', today())->count());
        $this->assertDatabaseCount('transaction_drafts', 12);
        $this->assertDatabaseCount('reference_records', 6);
        $this->assertSame(90, StockMovement::query()->where('type', 'sale')->distinct('transaction_id')->count('transaction_id'));
        $this->assertSame(0, Product::query()->where('stock_qty', '<', 0)->count());
        $this->assertSame(0, PosTransaction::query()->whereColumn('grand_total', '!=', DB::raw('subtotal - discount_amount + tax_amount'))->count());
        foreach (Product::query()->get() as $product) {
            $this->assertSame($product->stock_qty, StockMovement::query()->where('product_id', $product->id)->sum('delta_qty'));
        }

        $counts = collect(['users', 'products', 'transactions', 'transaction_items', 'stock_movements', 'transaction_drafts', 'customers', 'receivables', 'receivable_payments'])
            ->mapWithKeys(static fn (string $table): array => [$table => DB::table($table)->count()]);
        Product::query()->where('sku', 'DEMO-001')->update(['image_path' => 'products/custom.png']);
        Product::query()->where('sku', 'DEMO-002')->update(['image_path' => null]);
        Product::query()->where('sku', 'DEMO-002')->update(['barcode' => null]);
        Product::query()->where('sku', 'DEMO-003')->firstOrFail()->update(['barcode' => null]);
        Product::query()->where('sku', 'DEMO-003')->firstOrFail()->delete();
        Storage::disk('public')->put('products/custom.png', 'custom image');
        Storage::disk('public')->delete('products/demo/tea.webp');
        DB::table('users')->where('email', 'admin@gmail.com')->update(['password' => Hash::make('outdated-password')]);
        $this->seed(DatabaseSeeder::class);
        $this->assertTrue(Hash::check('admin123', DB::table('users')->where('email', 'admin@gmail.com')->value('password')));
        $this->assertSame('products/custom.png', Product::query()->where('sku', 'DEMO-001')->value('image_path'));
        $this->assertSame('products/demo/water.webp', Product::query()->where('sku', 'DEMO-002')->value('image_path'));
        $this->assertSame('8997000000029', Product::query()->where('sku', 'DEMO-002')->value('barcode'));
        $this->assertNull(Product::query()->where('sku', 'DEMO-003')->first());
        $this->assertNotNull(Product::withTrashed()->where('sku', 'DEMO-003')->firstOrFail()->deleted_at);
        $this->assertNull(Product::withTrashed()->where('sku', 'DEMO-003')->value('barcode'));
        Storage::disk('public')->assertExists('products/demo/tea.webp');
        foreach ($counts as $table => $count) {
            $this->assertDatabaseCount($table, $count);
        }
    }
}
