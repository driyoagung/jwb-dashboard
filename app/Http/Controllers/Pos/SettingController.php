<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\SettingRequest;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('pos.settings', ['settings' => StoreSetting::current()]);
    }

    public function update(SettingRequest $request): RedirectResponse
    {
        StoreSetting::current()->update($request->validated());

        return back()->with('status', 'Pengaturan toko berhasil disimpan.');
    }
}
