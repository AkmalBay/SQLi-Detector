<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->string('payment_method'); // Contoh: 'CASH', 'DEBIT', 'QRIS'
            $table->unsignedBigInteger('amount_paid'); // Jumlah yang dibayar dengan metode ini
            $table->string('reference_number')->nullable(); // Nomor kartu, kode QRIS, dll.
            $table->foreignId('pos_shift_id')->nullable()->constrained('pos_shifts'); // Penting untuk rekonsiliasi shift!
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_payments');
    }
};