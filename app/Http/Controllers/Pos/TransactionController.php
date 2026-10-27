<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosTransaction;
use App\Models\StoreSetting;
use App\Models\User;
use App\Services\MidtransPaymentService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'], 'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'payment_method' => ['nullable', 'in:cash,transfer,qris,other,midtrans,credit'],
            'status' => ['nullable', 'in:completed,pending,failed'],
            'cashier_id' => ['nullable', 'integer', 'exists:users,id'],
            'sort' => ['nullable', 'in:transaction_number,created_at,grand_total'],
            'direction' => ['nullable', 'in:asc,desc'],
        ]);
        $zone = StoreSetting::current()->timezone;
        $transactions = PosTransaction::query()->with('cashier')->withCount('items')
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('cashier_id', $request->user()->id))
            ->when($request->user()->isAdmin() && ($data['cashier_id'] ?? null), fn ($query) => $query->where('cashier_id', $data['cashier_id']))
            ->when($data['q'] ?? null, fn ($query, $q) => $query->where('transaction_number', 'like', '%'.$q.'%'))
            ->when($data['from'] ?? null, fn ($query, $from) => $query->where('created_at', '>=', CarbonImmutable::parse($from, $zone)->startOfDay()->utc()))
            ->when($data['to'] ?? null, fn ($query, $to) => $query->where('created_at', '<', CarbonImmutable::parse($to, $zone)->addDay()->startOfDay()->utc()))
            ->when($data['payment_method'] ?? null, fn ($query, $method) => $query->where('payment_method', $method))
            ->when($data['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->orderBy($data['sort'] ?? 'created_at', $data['direction'] ?? 'desc')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('pos.transactions.index', ['transactions' => $transactions, 'zone' => $zone, 'cashiers' => $request->user()->isAdmin() ? User::where('is_active', true)->orderBy('name')->pluck('name', 'id') : collect()]);
    }

    public function show(Request $request, PosTransaction $transaction): View
    {
        $this->authorizeTransaction($request, $transaction);

        return view('pos.transactions.show', ['transaction' => $transaction->load(['cashier', 'items', 'customer', 'receivable']), 'zone' => StoreSetting::current()->timezone]);
    }

    public function receipt(Request $request, PosTransaction $transaction): View
    {
        $this->authorizeTransaction($request, $transaction);
        abort_unless($transaction->status === 'completed', 404);

        return view('pos.transactions.receipt', ['transaction' => $transaction->load(['cashier', 'items', 'customer', 'receivable']), 'settings' => StoreSetting::current()]);
    }

    public function checkPayment(Request $request, PosTransaction $transaction, MidtransPaymentService $payments): RedirectResponse|JsonResponse
    {
        $this->authorizeTransaction($request, $transaction);
        abort_unless($transaction->payment_method === 'midtrans', 404);

        try {
            $payments->check($transaction);
        } catch (RuntimeException $exception) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $exception->getMessage()], 502);
            }

            return back()->withErrors(['payment' => $exception->getMessage()]);
        }

        if ($request->expectsJson()) {
            if ($transaction->status === 'completed') {
                $request->session()->flash('status', 'Pembayaran Midtrans berhasil dikonfirmasi.');
            }

            return response()->json(['status' => $transaction->status]);
        }

        return back()->with('status', match ($transaction->status) {
            'completed' => 'Pembayaran Midtrans berhasil dikonfirmasi.',
            'failed' => 'Pembayaran Midtrans gagal atau kedaluwarsa. Stok dikembalikan.',
            default => 'Pembayaran masih menunggu. Silakan cek kembali setelah membayar.',
        });
    }

    public function retryPayment(Request $request, PosTransaction $transaction, MidtransPaymentService $payments): RedirectResponse
    {
        $this->authorizeTransaction($request, $transaction);
        abort_unless($transaction->payment_method === 'midtrans' && $transaction->status === 'pending', 404);

        try {
            $payments->start($transaction);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['payment' => $exception->getMessage()]);
        }

        return back()->with('status', 'Sesi pembayaran Midtrans siap dibuka.');
    }

    private function authorizeTransaction(Request $request, PosTransaction $transaction): void
    {
        if (! $request->user()->isAdmin() && $transaction->cashier_id !== $request->user()->id) {
            abort(403);
        }
    }
}
