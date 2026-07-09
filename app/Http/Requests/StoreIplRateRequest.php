<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIplRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'house_type' => ['required', 'string', 'max:50'],
            'nominal' => ['required', 'integer', 'min:0'],
            'effective_date' => ['required', 'date'],
        ];
    }
}
