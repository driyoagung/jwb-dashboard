<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('store_settings')
            ->where('store_name', 'POS Jagat Demo')
            ->update(['store_name' => 'Griyo POS']);
    }

    public function down(): void
    {
        // The previous demo name is intentionally not restored over user edits.
    }
};
