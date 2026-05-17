<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_number',
        'user_id',
        'pos_shift_id',
        'customer_id',
        'total_amount',
        'amount_paid',
        'change',
        'total_price',
        'discount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change' => 'decimal:2',
            'total_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the items for the transaction.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Get the payments made for the transaction (Multi-Payment).
     */
    public function payments(): HasMany
    {
        return $this->hasMany(TransactionPayment::class);
    }

    /**
     * Get the user (Kasir) that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the POS Shift associated with the transaction.
     */
    public function posShift(): BelongsTo
    {
        return $this->belongsTo(PosShift::class);
    }

    /**
     * Get the customer associated with the transaction.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * ACCESSOR: Total amount formatted.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * ACCESSOR: Change formatted.
     */
    public function getFormattedChangeAttribute(): string
    {
        return 'Rp ' . number_format($this->change, 0, ',', '.');
    }

    /**
     * ACCESSOR: Get total items quantity.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * SCOPE: Filter by shift.
     */
    public function scopeByShift(Builder $query, int $shiftId): Builder
    {
        return $query->where('pos_shift_id', $shiftId);
    }

    /**
     * SCOPE: Filter by date range.
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * SCOPE: Filter by cashier.
     */
    public function scopeByCashier(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * SCOPE: Today's transactions.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * SCOPE: With items and product relationships.
     */
    public function scopeWithItems(Builder $query): Builder
    {
        return $query->with(['items.product', 'payments', 'user']);
    }
}