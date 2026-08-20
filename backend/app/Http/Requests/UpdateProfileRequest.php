<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_role'     => ['nullable', 'string', 'max:100'],
            'experience_level' => ['required', 'string', 'in:junior,mid,senior,lead'],
            'target_role'      => ['required', 'string', 'max:100'],
            'career_goal'      => ['nullable', 'string', 'max:500'],
        ];
    }
}
