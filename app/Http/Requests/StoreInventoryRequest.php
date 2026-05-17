<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
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
        $rules = [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,opname',
            'quantity' => 'required|integer|min:1|max:99999',
            'description' => 'nullable|string|max:500',
        ];

        // For 'in' type, supplier_id is required
        if ($this->input('type') === 'in') {
            $rules['supplier_id'] = 'nullable|exists:suppliers,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk harus dipilih.',
            'product_id.exists' => 'Produk tidak ditemukan.',
            'type.required' => 'Tipe pergerakan stok harus dipilih.',
            'type.in' => 'Tipe tidak valid. Pilih: Masuk, Keluar, atau Opname.',
            'quantity.required' => 'Jumlah stok harus diisi.',
            'quantity.min' => 'Jumlah minimal 1.',
            'supplier_id.exists' => 'Supplier tidak ditemukan.',
            'description.max' => 'Deskripsi terlalu panjang (maksimal 500 karakter).',
        ];
    }
}
