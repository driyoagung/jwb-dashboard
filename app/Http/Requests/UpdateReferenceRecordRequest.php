<?php

namespace App\Http\Requests;

class UpdateReferenceRecordRequest extends StoreReferenceRecordRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('record')) ?? false;
    }
}
