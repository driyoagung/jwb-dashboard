<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'sort' => ['nullable', 'in:name,email,role'], 'direction' => ['nullable', 'in:asc,desc']]);
        $users = User::query()->when($data['q'] ?? null, fn ($query, $q) => $query->where('name', 'like', '%'.$q.'%')->orWhere('email', 'like', '%'.$q.'%'))
            ->orderBy($data['sort'] ?? 'name', $data['direction'] ?? 'asc')->orderBy('id')->paginate(15)->withQueryString();

        return view('pos.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('pos.users.form', ['user' => null]);
    }

    public function edit(User $user): View
    {
        return view('pos.users.form', compact('user'));
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['password_confirmation']);
        User::create($data);

        return redirect()->route('pos.manage.users.index')->with('status', 'Pengguna berhasil dibuat.');
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        DB::transaction(function () use ($user, $data): void {
            User::query()->where('role', 'admin')->where('is_active', true)->orderBy('id')->lockForUpdate()->get();
            if ($user->isAdmin() && ($data['role'] !== 'admin' || ! $data['is_active']) && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
                throw ValidationException::withMessages(['role' => 'Admin aktif terakhir tidak dapat dinonaktifkan.']);
            }
            $user->update($data);
        }, 3);

        return redirect()->route('pos.manage.users.index')->with('status', 'Pengguna berhasil diperbarui.');
    }
}
