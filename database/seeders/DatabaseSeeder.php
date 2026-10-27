<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            $this->command?->warn('Data contoh hanya tersedia pada lingkungan local dan testing.');

            return;
        }

        $this->call(PosDemoSeeder::class);
    }
}
