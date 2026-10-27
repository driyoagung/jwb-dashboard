<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->timestamp('completed_at')->nullable()->change();
            $table->foreignId('draft_id')->nullable()->constrained('transaction_drafts')->nullOnDelete();
            $table->string('midtrans_status', 32)->nullable();
            $table->text('midtrans_redirect_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('draft_id');
            $table->dropColumn(['midtrans_status', 'midtrans_redirect_url']);
            $table->timestamp('completed_at')->nullable(false)->change();
        });
    }
};
