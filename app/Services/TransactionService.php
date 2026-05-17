<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\TransactionPayment;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\PosShift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    /**
     * Process a new transaction with stock validation and payment handling.
     *
     * @param array $validatedData
     * @return array
     * @throws \Exception
     */
    public function processTransaction(array $validatedData): array
    {
        $user = Auth::user();
        $activeShift = PosShift::where('user_id', $user->id)
            ->where('status', 'OPEN')
            ->first();

        if (!$activeShift) {
            throw new \Exception('Shift belum dibuka.');
        }

        $totalPayments = collect($validatedData['payments'])->sum('amount');
        if ($totalPayments < $validatedData['total_amount']) {
            throw new \Exception('Jumlah pembayaran kurang.');
        }

        DB::beginTransaction();

        try {
            $transaction = $this->createTransaction(
                $validatedData,
                $user,
                $activeShift
            );

            $this->processTransactionItems(
                $transaction,
                $validatedData['cart_items'],
                $user
            );

            $this->processPayments(
                $transaction,
                $validatedData['payments'],
                $activeShift
            );

            DB::commit();

            return [
                'success' => true,
                'transaction' => $transaction,
                'invoice_number' => $transaction->invoice_number,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create transaction header.
     */
    protected function createTransaction(
        array $data,
        $user,
        PosShift $activeShift
    ): Transaction {
        return Transaction::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'user_id' => $user->id,
            'pos_shift_id' => $activeShift->id,
            'customer_id' => $data['customer_id'] ?? null,
            'total_amount' => $data['total_amount'],
            'total_price' => $data['total_amount'],
            'amount_paid' => $data['amount_paid'],
            'change' => $data['change'],
        ]);
    }

    /**
     * Process transaction items with strict stock locking.
     */
    protected function processTransactionItems(
        Transaction $transaction,
        array $items,
        $user
    ): void {
        foreach ($items as $item) {
            $product = Product::where('id', $item['id'])
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw new \Exception("Produk dengan ID {$item['id']} tidak ditemukan.");
            }

            if ($product->stock < $item['quantity']) {
                throw new \Exception(
                    "Stok {$product->name} tidak cukup. Sisa: {$product->stock}"
                );
            }

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);

            $product->decrement('stock', $item['quantity']);

            InventoryLog::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'type' => 'out',
                'quantity' => $item['quantity'],
                'description' => 'Penjualan #' . $transaction->invoice_number,
            ]);
        }
    }

    /**
     * Process multi-payments for a transaction.
     */
    protected function processPayments(
        Transaction $transaction,
        array $payments,
        PosShift $activeShift
    ): void {
        foreach ($payments as $payment) {
            TransactionPayment::create([
                'transaction_id' => $transaction->id,
                'pos_shift_id' => $activeShift->id,
                'payment_method' => $payment['method'],
                'amount_paid' => $payment['amount'],
            ]);
        }
    }

    /**
     * Generate unique invoice number.
     */
    protected function generateInvoiceNumber(): string
    {
        return 'INV-' . time();
    }

    /**
     * Get transaction summary for a shift.
     */
    public function getShiftSummary(PosShift $shift): array
    {
        $cashPayments = TransactionPayment::where('pos_shift_id', $shift->id)
            ->where('payment_method', 'CASH')
            ->sum('amount_paid');

        $nonCashPayments = TransactionPayment::where('pos_shift_id', $shift->id)
            ->where('payment_method', '!=', 'CASH')
            ->sum('amount_paid');

        $expectedCash = $shift->starting_cash + $cashPayments;

        return [
            'cash_payments' => $cashPayments,
            'non_cash_payments' => $nonCashPayments,
            'expected_cash' => $expectedCash,
        ];
    }
}
