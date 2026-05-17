<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseShiftRequest extends FormRequest
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
            'actual_cash' => 'required|integer|min:0|max:999999999',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'actual_cash.required' => 'Jumlah uang tunai harus diisi.',
            'actual_cash.integer' => 'Jumlah uang tunai harus berupa angka bulat.',
            'actual_cash.min' => 'Jumlah uang tunai tidak boleh negatif.',
            'actual_cash.max' => 'Jumlah uang tunai terlalu besar.',
            'notes.max' => 'Catatan terlalu panjang (maksimal 1000 karakter).',
        ];
    }
}
