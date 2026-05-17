<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'total_amount' => 'required|numeric|min:1',
            'amount_paid' => 'required|numeric|min:1',
            'change' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'cart_items' => 'required|array|min:1',
            'cart_items.*.id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1|max:9999',
            'payments' => 'required|array|min:1',
            'payments.*.method' => 'required|string|in:CASH,DEBIT,QRIS,E_WALLET',
            'payments.*.amount' => 'required|numeric|min:1',
            'payments.*.reference_number' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'total_amount.required' => 'Total transaksi harus diisi.',
            'total_amount.min' => 'Total transaksi minimal 1.',
            'amount_paid.required' => 'Jumlah bayar harus diisi.',
            'cart_items.required' => 'Keranjang belanja tidak boleh kosong.',
            'cart_items.min' => 'Keranjang belanja minimal 1 item.',
            'cart_items.*.id.exists' => 'Produk tidak ditemukan.',
            'cart_items.*.quantity.min' => 'Jumlah barang minimal 1.',
            'payments.required' => 'Metode pembayaran harus dipilih.',
            'payments.min' => 'Pilih minimal 1 metode pembayaran.',
            'payments.*.method.required' => 'Metode pembayaran harus diisi.',
            'payments.*.method.in' => 'Metode pembayaran tidak valid.',
            'payments.*.amount.required' => 'Jumlah pembayaran harus diisi.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $totalPayments = collect($this->input('payments', []))->sum('amount');

            if ($totalPayments < $this->input('total_amount', 0)) {
                $validator->errors()->add(
                    'payments',
                    'Total pembayaran kurang dari total transaksi.'
                );
            }
        });
    }
}
