<?php

namespace App\Http\Requests\Pos;

use Illuminate\Validation\Rule;

class CheckoutRequest extends CartRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'idempotency_key' => ['required', 'uuid'],
            'payment_method' => ['required', Rule::in(['cash', 'transfer', 'qris', 'midtrans', 'credit'])],
            'amount_received' => ['required_unless:payment_method,midtrans,credit', 'nullable', 'integer', 'min:0', 'max:200000000000000'],
            'customer_id' => ['required_if:payment_method,credit', 'nullable', 'integer', Rule::exists('customers', 'id')->where('is_active', true)],
            'due_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
            'credit_note' => ['nullable', 'string', 'max:1000'],
            'draft_id' => ['nullable', 'integer', 'exists:transaction_drafts,id'],
            'acknowledge_changes' => ['nullable', 'boolean'],
        ]);
    }
}
