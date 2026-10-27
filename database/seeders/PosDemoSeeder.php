<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\PosNotification;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\Receivable;
use App\Models\ReferenceRecord;
use App\Models\StockMovement;
use App\Models\StoreSetting;
use App\Models\TransactionDraft;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PosDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            $this->command?->warn('Data contoh hanya tersedia pada lingkungan local dan testing.');

            return;
        }

        DB::transaction(function (): void {
            $users = $this->users();
            $settings = StoreSetting::query()->firstOrCreate(['id' => 1], [
                'store_name' => 'Griyo POS', 'phone' => '021-555-0188',
                'address' => 'Jl. Merdeka No. 18, Jakarta', 'receipt_footer' => 'Terima kasih telah berbelanja.',
                'tax_rate' => 11, 'draft_expiry_hours' => 24, 'max_active_drafts' => 20,
                'timezone' => 'Asia/Jakarta',
            ]);
            if ($settings->store_name === 'POS Jagat Demo') {
                $settings->update(['store_name' => 'Griyo POS']);
            }
            $products = $this->products($users[0]);
            $this->sales($users, $products, $settings);
            $this->credits($users);
            $this->notifications($users);
            $this->drafts($users, $products);
            $this->references($users[0]);
        });
    }

    /** @param list<User> $users */
    private function credits(array $users): void
    {
        $names = ['Budi Santoso', 'Siti Rahma', 'Andi Pratama', 'Rina Kartika', 'Dewi Lestari', 'Fajar Hidayat', 'Maya Putri', 'Rudi Hartono'];
        foreach ($names as $index => $name) {
            $customer = Customer::firstOrCreate(['phone' => sprintf('081200000%03d', $index + 1)], [
                'name' => $name, 'address' => 'Jakarta', 'is_active' => true,
            ]);
            $transaction = PosTransaction::where('transaction_number', sprintf('DEMO-POS-%04d', 81 + $index))->firstOrFail();
            if ($transaction->payment_method !== 'credit') {
                $transaction->update(['payment_method' => 'credit', 'customer_id' => $customer->id, 'amount_received' => 0, 'change_amount' => 0]);
            }
            $receivable = Receivable::firstOrCreate(['transaction_id' => $transaction->id], [
                'customer_id' => $customer->id, 'total_amount' => $transaction->grand_total,
                'due_date' => now()->addDays(($index - 3) * 5)->toDateString(),
                'note' => 'Contoh kasbon pelanggan.',
            ]);
            if ($index % 3 === 0 && $receivable->payments()->doesntExist()) {
                $amount = $index === 0 ? $receivable->total_amount : intdiv($receivable->total_amount, 2);
                $receivable->payments()->create([
                    'user_id' => $users[1]->id, 'amount' => $amount, 'method' => 'cash',
                    'idempotency_key' => sprintf('10000000-0000-4000-8000-%012d', $index + 1),
                    'note' => 'Pembayaran contoh.',
                ]);
                $receivable->update(['paid_amount' => $amount, 'status' => $amount === $receivable->total_amount ? 'paid' : 'partial']);
            }
        }
    }

    /** @return list<User> */
    private function users(): array
    {
        $accounts = [
            ['Admin Demo', 'admin@gmail.com', 'admin', true],
            ['Dewi Kasir', 'dewi@pos-jagat.test', 'cashier', true],
            ['Bagas Kasir', 'bagas@pos-jagat.test', 'cashier', true],
            ['Maya Kasir', 'maya@pos-jagat.test', 'cashier', true],
            ['Kasir Nonaktif', 'nonaktif@pos-jagat.test', 'cashier', false],
        ];

        return array_map(static function (array $account): User {
            $email = $account[1];
            $password = $email === 'admin@gmail.com' ? 'admin123' : 'password';
            $user = User::query()->firstOrCreate(
                ['email' => $email],
                ['name' => $account[0], 'role' => $account[2], 'is_active' => $account[3], 'password' => Hash::make($password)]
            );
            if ($email === 'admin@gmail.com' && (! $user->isAdmin() || ! Hash::check($password, $user->password))) {
                $user->update(['role' => 'admin', 'is_active' => true, 'password' => $password]);
            }

            return $user;
        }, $accounts);
    }

    /** @return list<Product> */
    private function products(User $admin): array
    {
        $categories = collect(['Minuman', 'Makanan', 'Camilan', 'Bahan pokok', 'Kebutuhan rumah', 'Perawatan diri'])
            ->mapWithKeys(static fn (string $name) => [$name => Category::query()->firstOrCreate(['name' => $name])]);
        $units = collect(['Pcs' => 'pcs', 'Botol' => 'btl', 'Bungkus' => 'bks', 'Kilogram' => 'kg'])
            ->mapWithKeys(static fn (string $symbol, string $name) => [$name => Unit::query()->firstOrCreate(['name' => $name], ['symbol' => $symbol])]);
        $catalog = [
            ['Minuman', 'Botol', [['Air Mineral 600 ml', 5000, 'water'], ['Air Mineral 1,5 L', 9000, 'water'], ['Teh Melati 350 ml', 7000, 'tea'], ['Kopi Susu 250 ml', 12000, 'coffee'], ['Jus Jeruk 300 ml', 11000, 'juice-milk'], ['Susu Cokelat 200 ml', 8500, 'juice-milk']]],
            ['Makanan', 'Bungkus', [['Roti Tawar', 18000, 'bread'], ['Roti Gandum', 23000, 'bread'], ['Mi Instan Goreng', 3500, 'noodles'], ['Mi Instan Kuah', 3500, 'noodles'], ['Biskuit Cokelat', 9500, 'biscuit-cereal'], ['Sereal Jagung', 27000, 'biscuit-cereal']]],
            ['Camilan', 'Bungkus', [['Keripik Singkong', 14000, 'chips-nuts'], ['Kacang Panggang', 17000, 'chips-nuts'], ['Wafer Vanila', 8000, 'sweet-snacks'], ['Permen Mint', 6000, 'sweet-snacks'], ['Cokelat Batang', 15000, 'sweet-snacks']]],
            ['Bahan pokok', 'Kilogram', [['Beras Premium 1 kg', 18000, 'staples'], ['Gula Pasir 1 kg', 17500, 'staples'], ['Tepung Terigu 1 kg', 14000, 'staples']]],
            ['Bahan pokok', 'Botol', [['Minyak Goreng 1 L', 21000, 'oil-soy'], ['Kecap Manis 600 ml', 19000, 'oil-soy']]],
            ['Kebutuhan rumah', 'Pcs', [['Sabun Cuci Piring', 16000, 'cleaning-liquid'], ['Deterjen Cair', 29000, 'cleaning-liquid'], ['Tisu Wajah', 13000, 'household'], ['Kantong Sampah', 12000, 'household'], ['Spons Cuci', 5000, 'household']]],
            ['Perawatan diri', 'Pcs', [['Sabun Mandi', 7000, 'body-care'], ['Sampo 170 ml', 22000, 'body-care'], ['Pasta Gigi 120 g', 18000, 'oral-sanitizer'], ['Sikat Gigi', 10000, 'oral-sanitizer'], ['Hand Sanitizer', 16000, 'oral-sanitizer']]],
        ];

        $products = [];
        $imagePaths = [];
        foreach ($catalog as [$category, $unit, $items]) {
            foreach ($items as [$name, $price, $image]) {
                $imagePaths[$image] ??= $this->publishProductImage($image);
                $number = count($products) + 1;
                $openingQty = $number === 32 ? 0 : ($number === 31 ? 5 : 100 + ($number * 17) % 61);
                $product = Product::withTrashed()->firstOrCreate(['sku' => sprintf('DEMO-%03d', $number)], [
                    'category_id' => $categories[$category]->id, 'unit_id' => $units[$unit]->id,
                    'barcode' => $this->demoBarcode($number), 'name' => $name,
                    'sell_price' => $price, 'cost_price' => intdiv($price * 7, 10),
                    'min_stock' => 10, 'is_active' => $number !== 32,
                    'description' => 'Produk contoh untuk pengujian POS.',
                    'image_path' => $imagePaths[$image],
                ]);
                if (! $product->image_path) {
                    $product->update(['image_path' => $imagePaths[$image]]);
                }
                if (! $product->trashed() && (! $product->barcode || $product->barcode === sprintf('8997000%06d', $number))
                    && ! Product::withTrashed()->where('barcode', $this->demoBarcode($number))->whereKeyNot($product->id)->exists()) {
                    $product->update(['barcode' => $this->demoBarcode($number)]);
                }
                if ($product->wasRecentlyCreated && $openingQty > 0) {
                    $product->stock_qty = $openingQty;
                    $product->save();
                    StockMovement::query()->create([
                        'product_id' => $product->id, 'user_id' => $admin->id,
                        'type' => 'in', 'delta_qty' => $openingQty, 'before_qty' => 0,
                        'after_qty' => $openingQty, 'reason' => 'Stok awal data contoh',
                    ])->forceFill(['created_at' => now()->subDays(31)])->save();
                }
                $products[] = $product;
            }
        }

        return $products;
    }

    private function demoBarcode(int $number): string
    {
        $body = sprintf('8997000%05d', $number);
        $sum = 0;
        foreach (str_split($body) as $index => $digit) {
            $sum += (int) $digit * ($index % 2 === 0 ? 1 : 3);
        }

        return $body.((10 - $sum % 10) % 10);
    }

    private function publishProductImage(string $name): string
    {
        $source = database_path('seeders/assets/products/'.$name.'.webp');
        if (! is_file($source)) {
            throw new RuntimeException("Gambar seeder tidak ditemukan: {$source}");
        }

        $path = 'products/demo/'.$name.'.webp';
        if (! Storage::disk('public')->exists($path)) {
            $contents = file_get_contents($source);
            if ($contents === false || ! Storage::disk('public')->put($path, $contents)) {
                throw new RuntimeException("Gambar seeder gagal disalin: {$path}");
            }
        }

        return $path;
    }

    /** @param list<User> $users
     * @param  list<Product>  $products
     */
    private function sales(array $users, array $products, StoreSetting $settings): void
    {
        $methods = ['cash', 'transfer', 'qris', 'other'];
        for ($number = 1; $number <= 90; $number++) {
            $code = sprintf('DEMO-POS-%04d', $number);
            if (PosTransaction::query()->where('transaction_number', $code)->exists()) {
                continue;
            }

            $cashier = $users[1 + ($number % 3)];
            $dayOffset = 29 - ($number % 30);
            $completedAt = $dayOffset === 0
                ? now()
                : now()->subDays($dayOffset)->setTime(9 + ($number % 11), ($number * 7) % 60);
            $lines = [];
            $subtotal = 0;
            foreach (range(0, 1 + $number % 3) as $offset) {
                $product = $products[($number * 7 + $offset * 11) % 30];
                $qty = 1 + ($number + $offset) % 3;
                $lines[] = [$product, $qty];
                $subtotal += $product->sell_price * $qty;
            }
            $discount = $number % 6 === 0 ? 2000 : 0;
            $tax = intdiv(($subtotal - $discount) * (int) round((float) $settings->tax_rate * 100) + 5000, 10000);
            $total = $subtotal - $discount + $tax;
            $method = $methods[$number % count($methods)];
            $received = $method === 'cash' ? intdiv($total + 9999, 10000) * 10000 : $total;
            $transaction = PosTransaction::query()->create([
                'transaction_number' => $code, 'cashier_id' => $cashier->id,
                'status' => 'completed', 'subtotal' => $subtotal,
                'discount_amount' => $discount, 'tax_rate' => $settings->tax_rate,
                'tax_amount' => $tax, 'grand_total' => $total,
                'payment_method' => $method, 'amount_received' => $received,
                'change_amount' => $received - $total,
                'idempotency_key' => sprintf('00000000-0000-4000-8000-%012d', $number),
                'completed_at' => $completedAt,
            ]);
            $transaction->forceFill(['created_at' => $completedAt, 'updated_at' => $completedAt])->save();

            foreach ($lines as [$product, $qty]) {
                $product->refresh();
                $before = $product->stock_qty;
                $after = $before - $qty;
                if ($after < 0) {
                    throw new RuntimeException("Stok data contoh tidak cukup untuk {$product->sku}.");
                }
                $transaction->items()->create([
                    'product_id' => $product->id, 'category_id' => $product->category_id,
                    'product_name' => $product->name, 'product_sku' => $product->sku,
                    'unit_name' => $product->unit->symbol, 'unit_price' => $product->sell_price,
                    'qty' => $qty, 'line_total' => $product->sell_price * $qty,
                ]);
                $product->stock_qty = $after;
                $product->save();
                StockMovement::query()->create([
                    'product_id' => $product->id, 'user_id' => $cashier->id,
                    'transaction_id' => $transaction->id, 'type' => 'sale',
                    'delta_qty' => -$qty, 'before_qty' => $before, 'after_qty' => $after,
                    'reason' => $code,
                ])->forceFill(['created_at' => $completedAt])->save();
            }
        }
    }

    /** @param list<User> $users
     * @param  list<Product>  $products
     */
    private function drafts(array $users, array $products): void
    {
        for ($number = 1; $number <= 12; $number++) {
            $cashier = $users[1 + ($number % 3)];
            $product = $products[($number * 3) % 30];
            $payload = ['seed_key' => "demo-{$number}", 'items' => [[
                'product_id' => $product->id, 'qty' => 1 + $number % 3,
                'unit_price' => $product->sell_price, 'product_name' => $product->name,
            ]], 'discount_amount' => $number % 4 === 0 ? 1000 : 0];
            if (TransactionDraft::query()->where('payload_json->seed_key', "demo-{$number}")->exists()) {
                continue;
            }
            TransactionDraft::query()->create([
                'cashier_id' => $cashier->id,
                'status' => $number <= 8 || $number > 10 ? 'active' : 'cancelled',
                'expires_at' => $number > 10 ? now()->subHours(2) : now()->addHours(2 + $number),
                'payload_json' => $payload,
            ]);
        }
    }

    private function references(User $admin): void
    {
        foreach (range(1, 6) as $number) {
            ReferenceRecord::query()->firstOrCreate(
                ['user_id' => $admin->id, 'title' => "Demo referensi {$number}"],
                ['category' => 'Panduan', 'status' => $number % 2 ? 'active' : 'draft',
                    'summary' => 'Contoh data untuk halaman referensi komponen.']
            );
        }
    }

    /** @param list<User> $users */
    private function notifications(array $users): void
    {
        foreach (PosTransaction::query()->where('transaction_number', 'like', 'DEMO-POS-%')->latest('id')->limit(10)->get() as $transaction) {
            foreach ([$users[0]->id, $transaction->cashier_id] as $userId) {
                PosNotification::query()->firstOrCreate(
                    ['user_id' => $userId, 'type' => 'sale', 'url' => route('pos.transactions.show', $transaction)],
                    ['title' => 'Transaksi baru', 'message' => $transaction->transaction_number.' selesai · Rp '.number_format($transaction->grand_total, 0, ',', '.')]
                );
            }
        }
    }
}
