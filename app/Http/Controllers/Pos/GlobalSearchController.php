<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\PosTransaction;
use App\Models\Product;
use App\Models\TransactionDraft;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GlobalSearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $term = trim($data['q'] ?? '');
        $products = collect();
        $transactions = collect();
        $drafts = collect();

        if (mb_strlen($term) >= 2) {
            $products = Product::query()
                ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('is_active', true))
                ->where(fn ($query) => $query
                    ->where('name', 'like', '%'.$term.'%')->orWhere('sku', 'like', '%'.$term.'%')
                    ->orWhere('barcode', 'like', '%'.$term.'%'))->orderBy('name')->limit(8)->get();
            $transactions = PosTransaction::query()->with('cashier')
                ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('cashier_id', $request->user()->id))
                ->where('transaction_number', 'like', '%'.$term.'%')->latest('id')->limit(8)->get();
            if (ctype_digit($term)) {
                $drafts = TransactionDraft::query()
                    ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('cashier_id', $request->user()->id))
                    ->whereKey((int) $term)->limit(1)->get();
            }
        }

        return view('pos.search', compact('term', 'products', 'transactions', 'drafts'));
    }
}
