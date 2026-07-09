<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $houseId = $this->route('house')?->id;

        return [
            'block' => ['required', 'string', 'max:10'],
            'house_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('houses')->where(fn ($q) => $q->where('block', $this->block))->ignore($houseId),
            ],
            'owner_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'type' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ];
    }
}
