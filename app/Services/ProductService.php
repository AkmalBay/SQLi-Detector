<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Create a new product with units and initial stock.
     *
     * @param array $data
     * @return Product
     * @throws \Exception
     */
    public function createProduct(array $data): Product
    {
        DB::beginTransaction();

        try {
            $product = Product::create([
                'sku' => $data['sku'],
                'name' => $data['name'],
                'category_id' => $data['category_id'],
                'supplier_id' => $data['supplier_id'],
                'stock' => $data['stock'] ?? 0,
                'price' => $data['price'],
            ]);

            // Create product units if provided
            if (isset($data['units']) && is_array($data['units'])) {
                $this->createProductUnits($product, $data['units']);
            }

            // Initialize stock if provided
            if (isset($data['stock']) && $data['stock'] > 0) {
                $inventoryService = new InventoryService();
                $inventoryService->initializeStock($product->id, $data['stock']);
            }

            DB::commit();

            return $product->load(['category', 'supplier', 'units']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing product with units.
     *
     * @param Product $product
     * @param array $data
     * @return Product
     * @throws \Exception
     */
    public function updateProduct(Product $product, array $data): Product
    {
        DB::beginTransaction();

        try {
            $product->update([
                'sku' => $data['sku'] ?? $product->sku,
                'name' => $data['name'] ?? $product->name,
                'category_id' => $data['category_id'] ?? $product->category_id,
                'supplier_id' => $data['supplier_id'] ?? $product->supplier_id,
                'price' => $data['price'] ?? $product->price,
            ]);

            // Update product units if provided
            if (isset($data['units']) && is_array($data['units'])) {
                $this->updateProductUnits($product, $data['units']);
            }

            DB::commit();

            return $product->load(['category', 'supplier', 'units']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create product units.
     */
    protected function createProductUnits(Product $product, array $units): void
    {
        foreach ($units as $unit) {
            if (!empty($unit['unit_name']) && !empty($unit['conversion_factor'])) {
                ProductUnit::create([
                    'product_id' => $product->id,
                    'unit_name' => $unit['unit_name'],
                    'conversion_factor' => $unit['conversion_factor'],
                    'price' => $unit['price'] ?? 0,
                ]);
            }
        }
    }

    /**
     * Update product units (replace existing).
     */
    protected function updateProductUnits(Product $product, array $units): void
    {
        // Remove existing units
        $product->units()->delete();

        // Create new units
        $this->createProductUnits($product, $units);
    }

    /**
     * Search products by keyword.
     *
     * @param string $keyword
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchProducts(string $keyword, int $limit = 10)
    {
        return Product::where('sku', 'like', "%{$keyword}%")
            ->orWhere('name', 'like', "%{$keyword}%")
            ->select('id', 'sku', 'name', 'price', 'stock')
            ->take($limit)
            ->get();
    }

    /**
     * Check if product has sufficient stock.
     *
     * @param Product $product
     * @param int $quantity
     * @return array
     */
    public function checkStockAvailability(Product $product, int $quantity = 1): array
    {
        return [
            'available' => $product->stock >= $quantity,
            'stock' => $product->stock,
            'requested' => $quantity,
            'message' => $product->stock < $quantity
                ? "Stok sisa {$product->stock}"
                : 'Stok tersedia',
        ];
    }

    /**
     * Get products with low stock.
     *
     * @param int $threshold
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockProducts(int $threshold = 10)
    {
        return Product::where('stock', '<=', $threshold)
            ->with(['category', 'supplier'])
            ->get();
    }

    /**
     * Delete a product.
     *
     * @param Product $product
     * @return bool
     * @throws \Exception
     */
    public function deleteProduct(Product $product): bool
    {
        if ($product->stock > 0) {
            throw new \Exception('Tidak dapat menghapus produk dengan stok tersisa.');
        }

        return $product->delete();
    }
}
