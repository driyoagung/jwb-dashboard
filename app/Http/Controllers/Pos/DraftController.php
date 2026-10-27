<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\CartRequest;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\TransactionDraft;
use App\Services\PosNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DraftController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:20'], 'status' => ['nullable', 'in:active,cancelled,completed,expired,pending_payment']]);
        $drafts = TransactionDraft::query()->with('cashier')
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('cashier_id', $request->user()->id))
            ->when($filters['q'] ?? null, fn ($query, $id) => ctype_digit($id) ? $query->whereKey($id) : $query->whereRaw('1 = 0'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $status === 'expired'
                ? $query->where('status', 'active')->where('expires_at', '<=', now())
                : $query->where('status', $status)->when($status === 'active', fn ($query) => $query->where('expires_at', '>', now())))
            ->orderByDesc('id')->paginate(15)->withQueryString();

        return view('pos.drafts.index', compact('drafts'));
    }

    public function store(CartRequest $request, PosNotifier $notifier): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $data, $notifier): void {
            $user = $request->user();
            $settings = StoreSetting::current();
            $user->newQuery()->whereKey($user->id)->lockForUpdate()->first();
            $count = TransactionDraft::where('cashier_id', $user->id)->where('status', 'active')->where('expires_at', '>', now())->count();
            if ($count >= $settings->max_active_drafts) {
                throw ValidationException::withMessages(['draft' => 'Batas draft aktif tercapai.']);
            }
            $draft = TransactionDraft::create([
                'cashier_id' => $user->id, 'status' => 'active',
                'expires_at' => now()->addHours($settings->draft_expiry_hours),
                'payload_json' => ['items' => $this->snapshot($data['items']), 'discount_amount' => (int) ($data['discount_amount'] ?? 0)],
            ]);
            $notifier->draft($draft, 'disimpan');
        }, 3);

        return redirect()->route('pos.drafts.index')->with('status', 'Draft berhasil disimpan.');
    }

    public function show(Request $request, TransactionDraft $draft): View
    {
        $this->authorizeDraft($request, $draft);
        $saved = collect($draft->payload_json['items'] ?? []);
        $products = Product::query()->whereIn('id', $saved->pluck('product_id'))->get()->keyBy('id');
        $changes = $saved->filter(fn ($item) => ! isset($products[$item['product_id']]) || ! $products[$item['product_id']]->is_active || $products[$item['product_id']]->stock_qty < $item['qty'] || $products[$item['product_id']]->sell_price !== (int) ($item['unit_price'] ?? -1));

        return view('pos.drafts.show', compact('draft', 'saved', 'products', 'changes'));
    }

    public function update(CartRequest $request, TransactionDraft $draft, PosNotifier $notifier): RedirectResponse
    {
        $this->authorizeDraft($request, $draft);
        $this->ensureActive($draft);
        $data = $request->validated();
        $draft->update(['payload_json' => ['items' => $this->snapshot($data['items']), 'discount_amount' => (int) ($data['discount_amount'] ?? 0)]]);
        $notifier->draft($draft, 'diperbarui');

        return redirect()->route('pos.drafts.show', $draft)->with('status', 'Draft diperbarui.');
    }

    public function destroy(Request $request, TransactionDraft $draft, PosNotifier $notifier): RedirectResponse
    {
        $this->authorizeDraft($request, $draft);
        if ($draft->status !== 'active') {
            throw ValidationException::withMessages(['draft' => 'Draft tidak aktif atau sedang menunggu pembayaran.']);
        }
        $draft->update(['status' => 'cancelled']);
        $notifier->draft($draft, 'dibatalkan');

        return redirect()->route('pos.drafts.index')->with('status', 'Draft dibatalkan.');
    }

    private function authorizeDraft(Request $request, TransactionDraft $draft): void
    {
        if (! $request->user()->isAdmin() && $draft->cashier_id !== $request->user()->id) {
            abort(403);
        }
    }

    private function ensureActive(TransactionDraft $draft): void
    {
        if ($draft->status !== 'active' || $draft->expires_at->isPast()) {
            throw ValidationException::withMessages(['draft' => 'Draft tidak aktif atau kedaluwarsa.']);
        }
    }

    /** @param array<int, array{product_id: int, qty: int}> $items
     * @return array<int, array{product_id: int, qty: int, unit_price: int, product_name: string}>
     */
    private function snapshot(array $items): array
    {
        $products = Product::query()->whereIn('id', array_column($items, 'product_id'))->get()->keyBy('id');

        return collect($items)->map(function ($item) use ($products): array {
            $product = $products[$item['product_id']];

            return ['product_id' => $product->id, 'qty' => (int) $item['qty'], 'unit_price' => $product->sell_price, 'product_name' => $product->name];
        })->all();
    }
}
