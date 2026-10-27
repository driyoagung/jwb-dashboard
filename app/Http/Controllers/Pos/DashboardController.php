<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\TransactionDraft;
use App\Services\SalesTrend;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, SalesTrend $trend): View
    {
        $settings = StoreSetting::current();
        $today = CarbonImmutable::now($settings->timezone)->startOfDay();
        $scoped = fn () => PosTransaction::query()->where('status', 'completed')
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('cashier_id', $request->user()->id));
        $start = $today->utc();
        $end = $today->addDay()->utc();
        $todayQuery = $scoped()->where('completed_at', '>=', $start)->where('completed_at', '<', $end);
        $revenue = (clone $todayQuery)->sum('grand_total');
        $count = (clone $todayQuery)->count();
        $drafts = TransactionDraft::query()->where('status', 'active')->where('expires_at', '>', now())
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('cashier_id', $request->user()->id))->count();
        $low = Product::query()->where('is_active', true)->where('min_stock', '>', 0)->whereColumn('stock_qty', '<=', 'min_stock');
        $lowCount = (clone $low)->count();
        $lowProducts = $request->user()->isAdmin() ? (clone $low)->orderBy('stock_qty')->limit(5)->get() : collect();
        $daily = $trend->dailyTotals($scoped(), $today->subDays(6), $today, $settings->timezone);
        $labels = array_map(fn ($day) => CarbonImmutable::parse($day, $settings->timezone)->format('d M'), array_keys($daily));
        $values = array_values($daily);
        $monthStart = $today->startOfMonth()->utc();
        $topProducts = DB::table('transaction_items as i')->join('transactions as t', 't.id', '=', 'i.transaction_id')
            ->select('i.product_sku', 'i.product_name')->selectRaw('SUM(i.qty) as qty, SUM(i.line_total) as revenue')
            ->where('t.status', 'completed')->where('t.completed_at', '>=', $monthStart)
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('t.cashier_id', $request->user()->id))
            ->groupBy('i.product_sku', 'i.product_name')->orderByDesc('qty')->limit(5)->get();

        return view('pos.dashboard', compact('revenue', 'count', 'drafts', 'lowCount', 'lowProducts', 'labels', 'values', 'topProducts'));
    }
}
