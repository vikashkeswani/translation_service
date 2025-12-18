<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class TranslationSearchRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => ['nullable', 'string'],
            'value' => ['nullable', 'string'],
            'tag' => ['nullable', 'string'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if (!$this->filled('key') && !$this->filled('value') && !$this->filled('tag')) {
                $validator->errors()->add('fields', 'At least one of key, value, or tag must be provided.');
            }
        });
    }
}
