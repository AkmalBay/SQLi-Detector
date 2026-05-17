<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'stock' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'units' => 'nullable|array',
            'units.*.unit_name' => 'nullable|string|max:50',
            'units.*.conversion_factor' => 'nullable|integer|min:1',
            'units.*.price' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'sku.required' => 'SKU harus diisi.',
            'sku.unique' => 'SKU sudah terdaftar.',
            'name.required' => 'Nama produk harus diisi.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'supplier_id.required' => 'Supplier harus dipilih.',
            'supplier_id.exists' => 'Supplier tidak valid.',
            'price.required' => 'Harga harus diisi.',
            'price.min' => 'Harga tidak boleh negatif.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'units.*.conversion_factor.min' => 'Faktor konversi harus minimal 1.',
        ];
    }
}
