<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Gunakan '*' agar data tersedia di SEMUA halaman (termasuk POS, Riwayat, dll)
        View::composer('*', function ($view) {
            
            // 1. Hitung Stok Menipis (JANGAN DIUBAH - Sudah Benar untuk Admin/Pemilik)
            $globalLowStock = Product::where('stock', '<=', 10)
                                     ->where('stock', '>', 0)
                                     ->count();

            // 2. Inisialisasi Variable
            $globalSalesToday = 0;   // Untuk menyimpan Total Uang (Rp)
            $myTransactionCount = 0; // TAMBAHAN: Untuk menyimpan Total Customer (Orang) khusus Kasir

            if (auth()->check()) {
                $user = auth()->user();

                if ($user->hasRole('pemilik')) {
                    // PEMILIK: (JANGAN DIUBAH)
                    $globalSalesToday = Transaction::whereDate('created_at', Carbon::today())
                                                   ->sum('total_price');
                } 
                elseif ($user->hasRole('kasir')) {
                    // KASIR:
                    
                    // A. Hitung Uang (JANGAN DIUBAH - Biarkan seperti kode Anda)
                    $globalSalesToday = Transaction::where('user_id', $user->id)
                                                   ->whereDate('created_at', Carbon::today())
                                                   ->sum('total_price');

                    // B. TAMBAHAN BARU: Hitung Jumlah Transaksi (Count)
                    // Ini yang bikin Quick Stats "X Cust" di sidebar Kasir jadi jalan
                    $myTransactionCount = Transaction::where('user_id', $user->id)
                                                     ->whereDate('created_at', Carbon::today())
                                                     ->count();
                }
            }

            // 3. Kirim variable ke View
            $view->with('globalLowStock', $globalLowStock);
            $view->with('globalSalesToday', $globalSalesToday);
            
            // Kirim variabel tambahan ini agar Sidebar Kasir tidak 0
            $view->with('myTransactionCount', $myTransactionCount); 
        });
    }
}