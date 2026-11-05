<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('price_type', 16);
            $table->unsignedBigInteger('old_price')->nullable();
            $table->unsignedBigInteger('new_price')->nullable();
            $table->timestamp('created_at');
            $table->index(['product_id', 'created_at', 'id']);
            $table->index(['price_type', 'created_at']);
        });
        Schema::table('transaction_items', function (Blueprint $table): void {
            $table->unsignedBigInteger('unit_cost')->nullable();
        });
        Schema::table('transactions', function (Blueprint $table): void {
            $table->unsignedBigInteger('cost_total')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', fn (Blueprint $table) => $table->dropColumn('cost_total'));
        Schema::table('transaction_items', fn (Blueprint $table) => $table->dropColumn('unit_cost'));
        Schema::dropIfExists('price_histories');
    }
};
