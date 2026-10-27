<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', Rule::exists('categories', 'id')->where(fn ($query) => $query->where('is_active', true)->orWhere('id', $this->route('product')?->category_id ?? 0))],
            'unit_id' => ['required', Rule::exists('units', 'id')->where(fn ($query) => $query->where('is_active', true)->orWhere('id', $this->route('product')?->unit_id ?? 0))],
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($this->route('product'))],
            'name' => ['required', 'string', 'max:255'],
            'sell_price' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'cost_price' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'min_stock' => ['required', 'integer', 'min:0', 'max:2000000000'],
            'is_active' => ['required', 'boolean'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'remove_image' => [$this->route('product') ? 'sometimes' : 'prohibited', 'boolean'],
            'initial_stock' => [$this->route('product') ? 'prohibited' : 'required', 'integer', 'min:0', 'max:2000000000'],
        ];
    }
}
