<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SalesTrend
{
    /** @return array<string, int> */
    public function dailyTotals(Builder $transactions, CarbonImmutable $firstDay, CarbonImmutable $lastDay, string $timezone): array
    {
        $minutes = (int) (CarbonImmutable::now($timezone)->offsetMinutes);
        $expression = DB::connection()->getDriverName() === 'sqlite'
            ? "DATE(datetime(completed_at, '{$minutes} minutes'))"
            : "DATE(DATE_ADD(completed_at, INTERVAL {$minutes} MINUTE))";
        $totals = (clone $transactions)
            ->where('completed_at', '>=', $firstDay->startOfDay()->utc())
            ->where('completed_at', '<', $lastDay->addDay()->startOfDay()->utc())
            ->selectRaw("{$expression} as local_day, SUM(grand_total) as amount")
            ->groupByRaw($expression)->pluck('amount', 'local_day');

        $result = [];
        for ($day = $firstDay; $day->lte($lastDay); $day = $day->addDay()) {
            $key = $day->format('Y-m-d');
            $result[$key] = (int) ($totals[$key] ?? 0);
        }

        return $result;
    }
}
