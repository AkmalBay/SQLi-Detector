<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('unit_name'); // Contoh: "Dus", "Pack", "Renteng"
            $table->integer('conversion_factor'); // Isi 1 unit ini berapa pcs? (Contoh: 40)
            $table->decimal('price', 10, 2); // Harga khusus untuk satuan ini (Contoh: 115.000)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};