<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreReceivablePaymentRequest;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use App\Services\PosNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReceivableController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:open,partial,paid,overdue'],
            'sort' => ['nullable', 'in:created_at,due_date,total_amount,balance,status'], 'direction' => ['nullable', 'in:asc,desc'],
        ]);
        $sort = $data['sort'] ?? 'created_at';
        $direction = $data['direction'] ?? 'desc';
        $receivables = Receivable::query()->with(['customer', 'transaction'])
            ->when($data['q'] ?? null, fn ($query, $q) => $query->where(fn ($query) => $query->whereHas('customer', fn ($query) => $query->where('name', 'like', '%'.$q.'%')->orWhere('phone', 'like', '%'.$q.'%'))->orWhereHas('transaction', fn ($query) => $query->where('transaction_number', 'like', '%'.$q.'%'))))
            ->when(($data['status'] ?? null) === 'overdue', fn ($query) => $query->where('status', '!=', 'paid')->whereDate('due_date', '<', today()))
            ->when(isset($data['status']) && $data['status'] !== 'overdue', fn ($query) => $query->where('status', $data['status']))
            ->when($sort === 'balance', fn ($query) => $query->orderByRaw('total_amount - paid_amount '.$direction), fn ($query) => $query->orderBy($sort, $direction))
            ->orderByDesc('id')->paginate(15)->withQueryString();
        $outstanding = Receivable::query()->where('status', '!=', 'paid')->selectRaw('COALESCE(SUM(total_amount - paid_amount), 0) as balance')->value('balance');

        return view('pos.receivables.index', compact('receivables', 'outstanding'));
    }

    public function show(Receivable $receivable): View
    {
        return view('pos.receivables.show', [
            'receivable' => $receivable->load(['customer', 'transaction', 'payments.user']),
            'key' => (string) Str::uuid(),
        ]);
    }

    public function storePayment(StoreReceivablePaymentRequest $request, Receivable $receivable, PosNotifier $notifier): RedirectResponse
    {
        $data = $request->validated();
        $existing = ReceivablePayment::where('idempotency_key', $data['idempotency_key'])->first();
        if ($existing) {
            abort_unless($existing->receivable_id === $receivable->id, 409);

            return redirect()->route('pos.receivables.show', $receivable)->with('status', 'Pembayaran sudah tercatat.');
        }

        DB::transaction(function () use ($request, $receivable, $data, $notifier): void {
            $locked = Receivable::query()->lockForUpdate()->findOrFail($receivable->id);
            if ($duplicate = ReceivablePayment::where('idempotency_key', $data['idempotency_key'])->first()) {
                abort_unless($duplicate->receivable_id === $locked->id, 409);

                return;
            }
            if ($data['amount'] > $locked->balance) {
                throw ValidationException::withMessages(['amount' => 'Nominal melebihi sisa tagihan.']);
            }
            $payment = $locked->payments()->create([
                'user_id' => $request->user()->id, 'amount' => $data['amount'], 'method' => $data['method'],
                'idempotency_key' => $data['idempotency_key'], 'note' => $data['note'] ?? null,
            ]);
            $locked->paid_amount += $payment->amount;
            $locked->status = $locked->balance === 0 ? 'paid' : 'partial';
            $locked->save();
            $notifier->receivablePayment($locked, $payment);
        }, 3);

        return redirect()->route('pos.receivables.show', $receivable)->with('status', 'Pembayaran kasbon berhasil dicatat.');
    }
}
