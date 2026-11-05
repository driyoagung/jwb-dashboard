<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table): void {
            $table->string('manual_transfer_provider', 64)->default('DANA');
            $table->string('manual_transfer_number', 100)->nullable();
            $table->string('manual_transfer_name', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table): void {
            $table->dropColumn(['manual_transfer_provider', 'manual_transfer_number', 'manual_transfer_name']);
        });
    }
};
