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
        $block = $this->block ?: null;

        return [
            'block' => ['nullable', 'string', 'max:10'],
            'house_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('houses')->where(fn ($q) => $block ? $q->where('block', $block) : $q->whereNull('block'))->ignore($houseId),
            ],
            'owner_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'nik' => ['required', 'digits:16', Rule::unique('houses')->ignore($houseId)],
            'ipl_status' => ['required', Rule::in(['rukem', 'non_rukem'])],
            'is_active' => ['boolean'],
        ];
    }
}
