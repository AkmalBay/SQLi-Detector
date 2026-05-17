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
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Kasir yang bertugas
            $table->dateTime('start_time'); // Waktu buka shift
            $table->dateTime('end_time')->nullable(); // Waktu tutup shift (null jika masih buka)
            
            $table->unsignedBigInteger('starting_cash')->default(0); // Modal Uang Tunai Awal
            
            // Kolom untuk rekonsiliasi saat tutup shift
            $table->unsignedBigInteger('expected_cash')->default(0); // Total uang yang seharusnya di laci
            $table->unsignedBigInteger('actual_cash')->default(0); // Total uang fisik yang dihitung
            $table->bigInteger('difference')->default(0); // Selisih (actual - expected)
            
            $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN'); // Status Shift
            $table->text('notes')->nullable(); // Catatan kasir saat penutupan
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_shifts');
    }
};