<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class InventoryService
{
    /**
     * Add stock to a product (Restock).
     *
     * @param int $productId
     * @param int $quantity
     * @param int|null $supplierId
     * @param string|null $description
     * @return void
     * @throws \Exception
     */
    public function addStock(
        int $productId,
        int $quantity,
        ?int $supplierId = null,
        ?string $description = null
    ): void {
        if ($quantity <= 0) {
            throw new \Exception('Jumlah stok harus lebih dari 0.');
        }

        $product = Product::find($productId);
        if (!$product) {
            throw new \Exception('Produk tidak ditemukan.');
        }

        $product->increment('stock', $quantity);

        InventoryLog::create([
            'product_id' => $productId,
            'user_id' => Auth::id(),
            'supplier_id' => $supplierId,
            'type' => 'in',
            'quantity' => $quantity,
            'description' => $description ?? "Restok +{$quantity}",
        ]);
    }

    /**
     * Remove stock from a product (Stock Out).
     *
     * @param int $productId
     * @param int $quantity
     * @param string|null $description
     * @return void
     * @throws \Exception
     */
    public function removeStock(
        int $productId,
        int $quantity,
        ?string $description = null
    ): void {
        if ($quantity <= 0) {
            throw new \Exception('Jumlah stok harus lebih dari 0.');
        }

        $product = Product::find($productId);
        if (!$product) {
            throw new \Exception('Produk tidak ditemukan.');
        }

        if ($product->stock < $quantity) {
            throw new \Exception(
                "Stok tidak mencukupi. Saat ini: {$product->stock}, diminta: {$quantity}"
            );
        }

        $product->decrement('stock', $quantity);

        InventoryLog::create([
            'product_id' => $productId,
            'user_id' => Auth::id(),
            'type' => 'out',
            'quantity' => $quantity,
            'description' => $description ?? "Keluar -{$quantity}",
        ]);
    }

    /**
     * Adjust stock to a specific amount (Stock Opname).
     *
     * @param int $productId
     * @param int $actualQuantity
     * @param string|null $description
     * @return void
     * @throws \Exception
     */
    public function adjustStock(
        int $productId,
        int $actualQuantity,
        ?string $description = null
    ): void {
        if ($actualQuantity < 0) {
            throw new \Exception('Jumlah stok tidak boleh negatif.');
        }

        $product = Product::find($productId);
        if (!$product) {
            throw new \Exception('Produk tidak ditemukan.');
        }

        $difference = $actualQuantity - $product->stock;

        if ($difference === 0) {
            return; // No adjustment needed
        }

        $product->update(['stock' => $actualQuantity]);

        InventoryLog::create([
            'product_id' => $productId,
            'user_id' => Auth::id(),
            'type' => 'opname',
            'quantity' => $difference,
            'description' => $description ?? "Stock Opname: {$product->stock} → {$actualQuantity}",
        ]);
    }

    /**
     * Initialize stock for a new product.
     *
     * @param int $productId
     * @param int $initialStock
     * @return void
     * @throws \Exception
     */
    public function initializeStock(int $productId, int $initialStock): void
    {
        if ($initialStock <= 0) {
            return; // No stock to initialize
        }

        $product = Product::find($productId);
        if (!$product) {
            throw new \Exception('Produk tidak ditemukan.');
        }

        $product->update(['stock' => $initialStock]);

        InventoryLog::create([
            'product_id' => $productId,
            'user_id' => Auth::id(),
            'type' => 'in',
            'quantity' => $initialStock,
            'description' => 'Stok Awal (Produk Baru)',
        ]);
    }

    /**
     * Get inventory logs with filters.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getInventoryLogs(array $filters = [])
    {
        $query = InventoryLog::with(['product', 'user', 'supplier']);

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query->latest();
    }
}
