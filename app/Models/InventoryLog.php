<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;

    // Guarded ['id'] artinya semua kolom boleh diisi kecuali kolom 'id'.
    // Ini penting agar fungsi create() di Controller berjalan lancar.
    protected $guarded = ['id'];

    /**
     * Relasi: Log ini milik Produk apa?
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi: Log ini dibuat oleh User siapa? (Admin Gudang/Pemilik)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Log ini berhubungan dengan Supplier mana? (Bisa null)
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}