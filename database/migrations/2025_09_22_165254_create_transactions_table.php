<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            
            // --- PERBAIKAN: Menambahkan User ID ---
            // Ini menghubungkan transaksi dengan User (Kasir/Pemilik)
            // onDelete('cascade') artinya jika User dihapus, transaksi ikut terhapus (opsional)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // --- Kolom Tambahan untuk Kelengkapan Transaksi ---
            $table->decimal('total_amount', 10, 2)->default(0); // Total akhir (setelah diskon)
            $table->decimal('amount_paid', 10, 2)->default(0);  // Uang yang dibayar
            $table->decimal('change', 10, 2)->default(0);       // Kembalian
            $table->decimal('discount', 10, 2)->default(0);     // Diskon
            
            // Relasi opsional
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('pos_shift_id')->nullable();

            $table->string('invoice_number')->unique();
            $table->decimal('total_price', 10, 2); // Subtotal (sebelum diskon)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};