<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class PosShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'starting_cash',
        'expected_cash',
        'actual_cash',
        'difference',
        'status',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'starting_cash' => 'integer',
            'expected_cash' => 'integer',
            'actual_cash' => 'integer',
            'difference' => 'integer',
        ];
    }

    /**
     * Relasi ke User (Kasir)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Pembayaran Transaksi
     */
    public function payments(): HasMany
    {
        return $this->hasMany(TransactionPayment::class);
    }

    /**
     * Relasi ke Transaksi
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * ACCESSOR: Cek apakah shift aktif.
     */
    public function getIsOpenAttribute(): bool
    {
        return $this->status === 'OPEN';
    }

    /**
     * ACCESSOR: Cek apakah shift sudah ditutup.
     */
    public function getIsClosedAttribute(): bool
    {
        return $this->status === 'CLOSED';
    }

    /**
     * ACCESSOR: Durasi shift dalam format readable.
     */
    public function getDurationAttribute(): ?string
    {
        if (!$this->end_time) {
            return $this->start_time->diffForHumans();
        }

        return $this->start_time->diff($this->end_time)->format('%H jam %I menit');
    }

    /**
     * ACCESSOR: Format selisih uang.
     */
    public function getFormattedDifferenceAttribute(): string
    {
        $diff = $this->difference;
        $prefix = $diff >= 0 ? '+' : '';

        return $prefix . 'Rp ' . number_format(abs($diff), 0, ',', '.');
    }

    /**
     * SCOPE: Filter hanya shift yang aktif.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'OPEN');
    }

    /**
     * SCOPE: Filter hanya shift yang sudah ditutup.
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'CLOSED');
    }

    /**
     * SCOPE: Filter shift berdasarkan kasir.
     */
    public function scopeByCashier(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * SCOPE: Filter shift berdasarkan tanggal.
     */
    public function scopeByDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * METHOD: Tutup shift.
     */
    public function close(int $actualCash, ?string $notes = null): bool
    {
        return $this->update([
            'end_time' => now(),
            'actual_cash' => $actualCash,
            'difference' => $actualCash - $this->expected_cash,
            'status' => 'CLOSED',
            'notes' => $notes,
        ]);
    }
}