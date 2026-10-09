<?php

namespace App\Http\Requests;

use App\Models\ReferenceRecord;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReferenceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', ReferenceRecord::class) ?? false;
    }

    /** @return array<string, array<int, string|ValidationRule>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['guide', 'report', 'note', 'archive'])],
            'status' => ['required', Rule::in(['draft', 'review', 'active', 'archive'])],
            'summary' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
