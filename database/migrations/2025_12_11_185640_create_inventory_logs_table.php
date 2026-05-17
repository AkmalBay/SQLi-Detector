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
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Produk (Jika produk dihapus, log ikut hilang)
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            // Relasi ke User (Siapa yang input)
            $table->foreignId('user_id')->constrained();
            
            // Relasi ke Supplier (Bisa Null/Kosong jika barang keluar/rusak)
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('type'); // in, out, opname
            $table->integer('quantity');
            $table->text('description')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};