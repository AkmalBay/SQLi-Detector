<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpenShiftRequest extends FormRequest
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
            'starting_cash' => 'required|integer|min:0|max:999999999',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'starting_cash.required' => 'Modal awal harus diisi.',
            'starting_cash.integer' => 'Modal awal harus berupa angka bulat.',
            'starting_cash.min' => 'Modal awal tidak boleh negatif.',
            'starting_cash.max' => 'Modal awal terlalu besar.',
        ];
    }
}
