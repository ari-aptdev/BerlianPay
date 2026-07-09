<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user()->id)],
            'reminder_email_enabled' => ['boolean'],
            'reminder_wa_enabled' => ['boolean'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ];
    }
}
