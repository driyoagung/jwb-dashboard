<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->index();
            $table->string('phone', 32)->nullable()->unique();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('transactions', function (Blueprint $table): void {
            $table->foreignId('customer_id')->nullable()->after('cashier_id')->constrained()->restrictOnDelete();
        });

        Schema::create('receivables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('transaction_id')->unique()->constrained('transactions')->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('paid_amount')->default(0);
            $table->string('status', 16)->default('open')->index();
            $table->date('due_date')->nullable()->index();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('receivable_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('receivable_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('method', 16);
            $table->uuid('idempotency_key')->unique();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['receivable_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receivable_payments');
        Schema::dropIfExists('receivables');
        Schema::table('transactions', fn (Blueprint $table) => $table->dropConstrainedForeignId('customer_id'));
        Schema::dropIfExists('customers');
    }
};
