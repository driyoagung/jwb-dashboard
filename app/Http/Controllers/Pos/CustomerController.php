<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', 'in:active,inactive'],
            'sort' => ['nullable', 'in:name,is_active,receivables_count'], 'direction' => ['nullable', 'in:asc,desc'],
        ]);
        $sort = $data['sort'] ?? 'name';
        $direction = $data['direction'] ?? 'asc';
        $customers = Customer::query()->withCount('receivables')
            ->when($data['q'] ?? null, fn ($query, $q) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$q.'%')->orWhere('phone', 'like', '%'.$q.'%')))
            ->when($data['status'] ?? null, fn ($query, $status) => $query->where('is_active', $status === 'active'))
            ->orderBy($sort, $direction)->orderBy('id')->paginate(15)->withQueryString();

        return view('pos.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('pos.customers.form', ['customer' => null]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = Customer::create($request->safe()->only(['name', 'phone', 'address']));

        if ($request->validated('return_to') === 'cashier') {
            return redirect()->route('pos.cashier', ['customer_id' => $customer->id])->with('status', 'Pelanggan berhasil ditambahkan.');
        }

        return redirect()->route('pos.customers.index')->with('status', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer): View
    {
        return view('pos.customers.form', compact('customer'));
    }

    public function update(StoreCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->safe()->only(['name', 'phone', 'address', 'is_active']));

        return redirect()->route('pos.customers.index')->with('status', 'Pelanggan berhasil diperbarui.');
    }
}
