<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Kasir yang hold
            $table->string('reference_name')->nullable(); // Nama referensi (misal: "Bapak Baju Merah")
            $table->json('items'); // Data item disimpan dalam format JSON
            $table->decimal('total_amount', 15, 2); // Total sementara
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_holds');
    }
};