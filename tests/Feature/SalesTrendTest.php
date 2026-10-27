<?php

namespace Tests\Feature;

use App\Models\PosTransaction;
use App\Models\User;
use App\Services\SalesTrend;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SalesTrendTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_trend_uses_store_timezone_and_fills_empty_day(): void
    {
        $cashier = User::factory()->create();
        PosTransaction::create([
            'transaction_number' => 'POS-TEST-1', 'cashier_id' => $cashier->id, 'status' => 'completed',
            'subtotal' => 5000, 'discount_amount' => 0, 'tax_rate' => 0, 'tax_amount' => 0,
            'grand_total' => 5000, 'payment_method' => 'cash', 'amount_received' => 5000,
            'change_amount' => 0, 'idempotency_key' => (string) Str::uuid(),
            'completed_at' => '2026-10-16 18:00:00',
        ]);
        $trend = app(SalesTrend::class)->dailyTotals(
            PosTransaction::query()->where('status', 'completed'),
            CarbonImmutable::parse('2026-10-16', 'Asia/Jakarta'),
            CarbonImmutable::parse('2026-10-17', 'Asia/Jakarta'),
            'Asia/Jakarta',
        );
        $this->assertSame(['2026-10-16' => 0, '2026-10-17' => 5000], $trend);
    }
}
