<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'supplier_id',
        'stock',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier that owns the product.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the transaction items for the product.
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * RELASI BARU: Multi Satuan
     * Satu produk punya banyak satuan tambahan (Dus, Pack, dll)
     */
    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    /**
     * ACCESSOR: Format harga ke format Rupiah.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * ACCESSOR: Cek apakah stok rendah.
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= config('app_constants.inventory.low_stock_threshold', 10);
    }

    /**
     * SCOPE: Filter produk dengan stok rendah.
     */
    public function scopeLowStock(Builder $query, ?int $threshold = null): Builder
    {
        $threshold = $threshold ?? config('app_constants.inventory.low_stock_threshold', 10);
        return $query->where('stock', '<=', $threshold);
    }

    /**
     * SCOPE: Filter berdasarkan kategori.
     */
    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * SCOPE: Filter berdasarkan supplier.
     */
    public function scopeBySupplier(Builder $query, int $supplierId): Builder
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * SCOPE: Cari produk berdasarkan keyword.
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('sku', 'like', "%{$keyword}%")
                ->orWhere('name', 'like', "%{$keyword}%");
        });
    }

    /**
     * SCOPE: Produk yang tersedia (stok > 0).
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * METHOD: Kurangi stok dengan validasi.
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock < $quantity) {
            return false;
        }

        return $this->decrement('stock', $quantity);
    }

    /**
     * METHOD: Tambah stok.
     */
    public function increaseStock(int $quantity): bool
    {
        return $this->increment('stock', $quantity);
    }
}