<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReceivablePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('pos.sell') ?? false;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1', 'max:200000000000000'],
            'method' => ['required', Rule::in(['cash', 'transfer', 'qris'])],
            'idempotency_key' => ['required', 'uuid'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
