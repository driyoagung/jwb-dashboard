<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 16)->default('cashier')->index();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('store_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('store_name')->default('Toko');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('receipt_footer')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->unsignedSmallInteger('draft_expiry_hours')->default(24);
            $table->unsignedSmallInteger('max_active_drafts')->default(20);
            $table->string('timezone')->default('Asia/Jakarta');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('symbol', 24)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('unit_id')->constrained()->restrictOnDelete();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name')->index();
            $table->unsignedBigInteger('sell_price');
            $table->unsignedBigInteger('cost_price')->nullable();
            $table->unsignedInteger('stock_qty')->default(0);
            $table->unsignedInteger('min_stock')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table): void {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 16)->default('completed');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->decimal('tax_rate', 5, 2);
            $table->unsignedBigInteger('tax_amount');
            $table->unsignedBigInteger('grand_total');
            $table->string('payment_method', 16);
            $table->unsignedBigInteger('amount_received');
            $table->unsignedBigInteger('change_amount')->default(0);
            $table->uuid('idempotency_key')->unique();
            $table->timestamp('completed_at')->index();
            $table->timestamps();
            $table->index(['cashier_id', 'completed_at']);
        });

        Schema::create('transaction_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_sku');
            $table->string('unit_name');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedInteger('qty');
            $table->unsignedBigInteger('line_total');
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('type', 24);
            $table->integer('delta_qty');
            $table->unsignedInteger('before_qty');
            $table->unsignedInteger('after_qty');
            $table->text('reason');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['product_id', 'created_at']);
        });

        Schema::create('transaction_drafts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->string('status', 16)->default('active');
            $table->timestamp('expires_at')->index();
            $table->json('payload_json');
            $table->timestamps();
            $table->index(['cashier_id', 'status', 'expires_at']);
        });
    }

    public function down(): void
    {
        foreach (['transaction_drafts', 'stock_movements', 'transaction_items', 'transactions', 'products', 'units', 'categories', 'store_settings'] as $name) {
            Schema::dropIfExists($name);
        }
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['role', 'is_active']);
        });
    }
};
