<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\TransactionDraft;
use App\Models\User;
use App\Services\SalesTrend;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function show(Request $request, string $report, SalesTrend $trend): View
    {
        $this->ensureType($report);
        [$filters, $start, $end] = $this->filters($request);
        $title = ['sales' => 'Penjualan', 'products' => 'Produk terlaris', 'stock' => 'Stok', 'drafts' => 'Draft transaksi'][$report];
        $rows = match ($report) {
            'sales' => $this->sales($start, $end)->with('cashier')->orderByDesc('completed_at')->paginate(20)->withQueryString(),
            'products' => $this->products($start, $end, $filters)->paginate(20)->withQueryString(),
            'stock' => $this->stock($filters)->with(['category', 'unit'])->orderBy('name')->paginate(20)->withQueryString(),
            'drafts' => $this->drafts($start, $end, $filters)->with('cashier')->orderByDesc('id')->paginate(20)->withQueryString(),
        };
        $summary = match ($report) {
            'sales' => ['Total omzet' => 'Rp '.number_format($this->sales($start, $end)->sum('grand_total'), 0, ',', '.'), 'Transaksi' => $this->sales($start, $end)->count(), 'Rata-rata' => 'Rp '.number_format((int) round($this->sales($start, $end)->avg('grand_total') ?? 0), 0, ',', '.')],
            'products' => ['Produk terjual' => DB::query()->fromSub($this->products($start, $end, $filters), 'ranked')->count()],
            'stock' => ['Produk' => $this->stock($filters)->count(), 'Stok menipis' => Product::where('min_stock', '>', 0)->whereColumn('stock_qty', '<=', 'min_stock')->count(), 'Nilai estimasi' => 'Rp '.number_format($this->stock($filters)->selectRaw('SUM(stock_qty * cost_price) as value')->value('value') ?? 0, 0, ',', '.')],
            'drafts' => ['Draft dalam periode' => $this->drafts($start, $end, $filters)->count(), 'Nilai estimasi' => 'Rp '.number_format($this->drafts($start, $end, $filters)->orderBy('id')->lazy(500)->sum(fn ($draft) => max(0, collect($draft->payload_json['items'] ?? [])->sum(fn ($item) => ($item['unit_price'] ?? 0) * ($item['qty'] ?? 0)) - ($draft->payload_json['discount_amount'] ?? 0))), 0, ',', '.')],
        };
        $labels = [];
        $values = [];
        if ($report === 'sales') {
            $zone = StoreSetting::current()->timezone;
            $first = CarbonImmutable::parse($filters['from'], $zone);
            $last = CarbonImmutable::parse($filters['to'], $zone);
            $daily = $trend->dailyTotals(PosTransaction::query()->where('status', 'completed'), $first, $last, $zone);
            $labels = array_map(fn ($day) => CarbonImmutable::parse($day, $zone)->format('d M'), array_keys($daily));
            $values = array_values($daily);
        }

        $paymentBreakdown = $report === 'sales' ? $this->sales($start, $end)->select('payment_method')->selectRaw('COUNT(*) as transactions, SUM(grand_total) as total')->groupBy('payment_method')->get() : collect();
        $categories = $report === 'products' ? Category::orderBy('name')->pluck('name', 'id') : collect();
        $cashiers = $report === 'drafts' ? User::where('is_active', true)->orderBy('name')->pluck('name', 'id') : collect();

        return view('pos.reports.show', compact('report', 'title', 'rows', 'summary', 'filters', 'labels', 'values', 'paymentBreakdown', 'categories', 'cashiers'));
    }

    public function export(Request $request, string $report): StreamedResponse
    {
        $this->ensureType($report);
        [$filters, $start, $end] = $this->filters($request);

        return response()->streamDownload(function () use ($report, $start, $end, $filters): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            $headers = match ($report) {
                'sales' => ['Nomor', 'Waktu', 'Kasir', 'Metode', 'Total'],
                'products' => ['SKU', 'Produk', 'Qty', 'Omzet bruto'],
                'stock' => ['SKU', 'Produk', 'Stok', 'Minimum', 'Harga modal', 'Nilai estimasi'],
                'drafts' => ['ID', 'Kasir', 'Status', 'Dibuat', 'Kedaluwarsa'],
            };
            fputcsv($out, $headers);
            $query = match ($report) {
                'sales' => $this->sales($start, $end)->with('cashier')->orderBy('id'),
                'products' => $this->products($start, $end, $filters)->orderBy('product_sku'),
                'stock' => $this->stock($filters)->orderBy('id'),
                'drafts' => $this->drafts($start, $end, $filters)->with('cashier')->orderBy('id'),
            };
            $records = $report === 'products' ? $query->cursor() : $query->lazyById(500);
            foreach ($records as $row) {
                $values = match ($report) {
                    'sales' => [$row->transaction_number, $row->completed_at, $row->cashier->name, $row->payment_method, $row->grand_total],
                    'products' => [$row->product_sku, $row->product_name, $row->qty, $row->revenue],
                    'stock' => [$row->sku, $row->name, $row->stock_qty, $row->min_stock, $row->cost_price, $row->cost_price === null ? '' : $row->stock_qty * $row->cost_price],
                    'drafts' => [$row->id, $row->cashier->name, $row->status === 'active' && $row->expires_at->isPast() ? 'expired' : $row->status, $row->created_at, $row->expires_at],
                };
                fputcsv($out, array_map(static function ($value): string {
                    $value = (string) $value;

                    return preg_match('/^[\s\x00-\x1F]*[=+\-@]/u', $value) ? "'".$value : $value;
                }, $values));
            }
            fclose($out);
        }, "laporan-{$report}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function ensureType(string $type): void
    {
        if (! in_array($type, ['sales', 'products', 'stock', 'drafts'], true)) {
            abort(404);
        }
    }

    /** @return array{0: array<string, string>, 1: CarbonImmutable, 2: CarbonImmutable} */
    private function filters(Request $request): array
    {
        $zone = StoreSetting::current()->timezone;
        $data = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'stock_status' => ['nullable', 'in:all,low,out,safe'],
            'draft_status' => ['nullable', 'in:all,active,expired,cancelled,completed,pending_payment'],
            'cashier_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);
        $today = CarbonImmutable::now($zone)->startOfDay();
        $from = CarbonImmutable::parse($data['from'] ?? $today->subDays(29)->format('Y-m-d'), $zone)->startOfDay();
        $to = CarbonImmutable::parse($data['to'] ?? $today->format('Y-m-d'), $zone)->startOfDay();
        if ($to->diffInDays($from) > 366 || $to->lt($from)) {
            abort(422, 'Rentang tanggal tidak valid.');
        }

        return [array_merge($data, ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]), $from->utc(), $to->addDay()->utc()];
    }

    private function sales(CarbonImmutable $start, CarbonImmutable $end): Builder
    {
        return PosTransaction::query()->where('status', 'completed')->where('completed_at', '>=', $start)->where('completed_at', '<', $end);
    }

    private function products(CarbonImmutable $start, CarbonImmutable $end, array $filters): \Illuminate\Database\Query\Builder
    {
        return DB::table('transaction_items as i')->join('transactions as t', 't.id', '=', 'i.transaction_id')
            ->select('i.product_sku', 'i.product_name')->selectRaw('SUM(i.qty) as qty, SUM(i.line_total) as revenue')
            ->where('t.status', 'completed')->where('t.completed_at', '>=', $start)->where('t.completed_at', '<', $end)
            ->when($filters['category_id'] ?? null, fn ($query, $id) => $query->where('i.category_id', $id))
            ->groupBy('i.product_sku', 'i.product_name')->orderByDesc('qty');
    }

    private function stock(array $filters): Builder
    {
        return Product::query()->when($filters['stock_status'] ?? null, function ($query, $status): void {
            match ($status) {
                'out' => $query->where('stock_qty', 0),
                'low' => $query->where('stock_qty', '>', 0)->where('min_stock', '>', 0)->whereColumn('stock_qty', '<=', 'min_stock'),
                'safe' => $query->where('stock_qty', '>', 0)->where(fn ($query) => $query->where('min_stock', 0)->orWhereColumn('stock_qty', '>', 'min_stock')),
                default => null,
            };
        });
    }

    private function drafts(CarbonImmutable $start, CarbonImmutable $end, array $filters): Builder
    {
        return TransactionDraft::query()->where('created_at', '>=', $start)->where('created_at', '<', $end)
            ->when($filters['cashier_id'] ?? null, fn ($query, $id) => $query->where('cashier_id', $id))
            ->when($filters['draft_status'] ?? null, function ($query, $status): void {
                match ($status) {
                    'expired' => $query->where('status', 'active')->where('expires_at', '<=', now()),
                    'active' => $query->where('status', 'active')->where('expires_at', '>', now()),
                    'cancelled', 'completed', 'pending_payment' => $query->where('status', $status),
                    default => null,
                };
            });
    }
}
