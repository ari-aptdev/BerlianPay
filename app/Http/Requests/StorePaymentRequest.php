<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'house_id' => ['required', 'exists:houses,id'],
            'period_month' => ['required', 'integer', 'between:1,12'],
            'period_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'amount' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['paid', 'unpaid'])],
            'proof_image' => ['nullable', 'image', 'max:4096'],
            'signature' => ['nullable', 'string'],
            'paid_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'house_id.required' => 'Rumah wajib dipilih.',
            'proof_image.image' => 'Bukti pembayaran harus berupa gambar (jpg/png).',
            'proof_image.max' => 'Ukuran bukti pembayaran maksimal 4MB.',
        ];
    }
}
