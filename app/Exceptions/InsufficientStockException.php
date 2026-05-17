<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected $productId;
    protected $availableStock;
    protected $requestedQuantity;

    public function __construct(
        int $productId,
        int $availableStock,
        int $requestedQuantity,
        ?string $productName = null
    ) {
        $product = $productName ?? "Produk #{$productId}";
        $message = "Stok {$product} tidak mencukupi. Tersedia: {$availableStock}, Diminta: {$requestedQuantity}";

        parent::__construct($message);

        $this->productId = $productId;
        $this->availableStock = $availableStock;
        $this->requestedQuantity = $requestedQuantity;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getAvailableStock(): int
    {
        return $this->availableStock;
    }

    public function getRequestedQuantity(): int
    {
        return $this->requestedQuantity;
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error' => 'insufficient_stock',
            'data' => [
                'product_id' => $this->productId,
                'available_stock' => $this->availableStock,
                'requested_quantity' => $this->requestedQuantity,
            ],
        ], 400);
    }
}
