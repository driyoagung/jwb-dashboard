<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class IndexPriceHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('pos.admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'price_type' => ['nullable', 'in:cost,sell'],
        ];
    }
}
