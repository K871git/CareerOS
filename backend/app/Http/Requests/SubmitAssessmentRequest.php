<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'answers'                          => ['required', 'array', 'min:1'],
            'answers.*.question_id'            => ['required', 'integer', 'exists:questions,id'],
            'answers.*.selected_option_id'     => ['required', 'integer', 'exists:question_options,id'],
        ];
    }
}
