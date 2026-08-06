<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCareerAssessmentRequest extends FormRequest
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
            'target_role'        => ['required', 'string', 'max:100'],
            'skills'             => ['required', 'array', 'min:1'],
            'skills.*.skill_id'  => ['required', 'integer', 'exists:skills,id'],
            'skills.*.level'     => ['required', 'string'],
            'skills.*.score'     => ['required', 'integer', 'between:0,100'],
        ];
    }
}
