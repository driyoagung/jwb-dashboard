<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:255'], 'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'], 'receipt_footer' => ['nullable', 'string', 'max:1000'],
            'tax_rate' => ['required', 'numeric', 'between:0,100', 'decimal:0,2'],
            'draft_expiry_hours' => ['required', 'integer', 'between:1,720'],
            'max_active_drafts' => ['required', 'integer', 'between:1,100'],
            'timezone' => ['required', Rule::in(['Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura'])],
        ];
    }
}
