<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'mobile'            => ['required', 'string', 'max:20', 'regex:/^\+?[0-9]{10,15}$/', 'unique:users,mobile'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'consent_accepted'  => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'consent_accepted.required' => 'You must agree to the Terms & Conditions and Privacy Policy to create an account.',
            'consent_accepted.accepted'  => 'You must agree to the Terms & Conditions and Privacy Policy to create an account.',
        ];
    }
}
