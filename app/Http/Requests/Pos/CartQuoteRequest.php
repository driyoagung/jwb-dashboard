<?php

namespace App\Http\Requests\Pos;

class CartQuoteRequest extends CartRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'items.*.product_id' => ['required', 'integer', 'distinct', 'min:1'],
        ]);
    }
}
