<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\POSController; 

// 🔴 LAPISAN PERTAHANAN AI (GLOBAL WAF UNTUK WEB.PHP)
// Semua rute di dalam grup ini akan diperiksa oleh model Naive Bayes
Route::middleware([\App\Http\Middleware\SQLiDefense::class])->group(function () {

    // Login Routes (Sekarang aman dari SQLi)
    Auth::routes(['register' => false]);

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // ========================================================================
    // AREA KHUSUS PENGGUNA LOGIN
    // ========================================================================
    Route::middleware(['auth'])->group(function () {
        
        // Dashboard bisa diakses semua user yang login
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Inventory: Hanya role terkait yang bisa akses
        Route::middleware(['role:admin_gudang|pemilik|pekerja_gudang'])->group(function () {
            Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        });

        // Pengumuman (Akses Semua User)
        Route::get('/announcements', [\App\Http\Controllers\AnnouncementController::class, 'list'])->name('announcements.index');

        // --- GRUP Common Read: ADMIN, PEMILIK, PEKERJA ---
        Route::middleware(['role:admin_gudang|pemilik|pekerja_gudang'])->group(function () {
             Route::get('/products', [ProductController::class, 'index'])->name('products.index');
             Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        });

        // --- GRUP Product Create: ADMIN, PEMILIK, PEKERJA ---
        Route::middleware(['role:admin_gudang|pemilik|pekerja_gudang'])->group(function () {
            Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        });

        // --- GRUP Admin Full Control: ADMIN GUDANG & PEMILIK ---
        Route::middleware(['role:admin_gudang|pemilik'])->group(function () {
            // Product: Edit, Update, Destroy (Restricted)
            Route::resource('products', ProductController::class)->except(['index', 'create', 'store']);

            // Supplier: Full CRUD except Index
            Route::resource('suppliers', SupplierController::class)->except(['index']);

            // Route untuk Simpan Stok (Restock/Barang Keluar)
            Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store'); 
        });

        // --- GRUP 2A: KHUSUS PEMILIK (Fokus: Laporan & Manajemen) ---
        Route::middleware(['role:pemilik'])->group(function () {
            // Laporan
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
            
            // Manajemen Pengumuman (SEKARANG SUDAH AMAN)
            Route::resource('announcements/manage', \App\Http\Controllers\AnnouncementController::class, [
                'names' => 'announcements.manage',
                'parameters' => [
                    'manage' => 'announcement'
                ]
            ]);
            
            // Manajemen User / Karyawan
            Route::resource('employees', \App\Http\Controllers\EmployeeController::class);

            // Kelola Akun (Profil Pemilik)
            Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
            Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        });

        // --- GRUP 2B: KASIR & PEMILIK (Fokus: Transaksi & POS System) ---
        Route::middleware(['role:kasir|pemilik'])->group(function () {

            // 1. Halaman Utama POS
            Route::get('/pos', [POSController::class, 'index'])->name('pos.index');

            // 2. Manajemen Shift (Buka & Tutup)
            Route::get('/pos/shift/open', [POSController::class, 'openShiftForm'])->name('pos.shift.open.form');
            Route::post('/pos/shift/open', [POSController::class, 'openShift'])->name('pos.shift.open');
            Route::get('/pos/shift/close', [POSController::class, 'closeShiftForm'])->name('pos.shift.close.form');
            Route::post('/pos/shift/close/{shift}', [POSController::class, 'closeShift'])->name('pos.shift.close');

            // Riwayat Laporan Setoran (Shift History)
            Route::get('/pos/shifts', [\App\Http\Controllers\ShiftReportController::class, 'index'])->name('pos.shifts.index');

            // 3. API/AJAX Endpoint POS
            Route::prefix('api/pos')->group(function() {
                Route::get('/search', [POSController::class, 'searchProduct'])->name('pos.search');
                Route::post('/check-stock/{product}', [POSController::class, 'checkStock'])->name('pos.check.stock');
                Route::post('/transaction', [POSController::class, 'storeTransaction'])->name('pos.transaction.store');

                // Fitur Hold Cart
                Route::post('/hold', [POSController::class, 'holdCart'])->name('pos.hold');
                Route::post('/restore/{id}', [POSController::class, 'restoreCart'])->name('pos.restore');
                Route::delete('/remove/{id}', [POSController::class, 'removeHeldCart'])->name('pos.remove');
            });

            // 4. Riwayat Transaksi / Invoice
            Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
            Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
            Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
            Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

            // Route Invoice Thermal
            Route::get('/transactions/{transaction}/invoice', [TransactionController::class, 'invoice'])->name('transactions.invoice');
        });

        // --- GRUP 2C: KHUSUS PEMILIK (Void Transaksi) ---
        Route::middleware(['role:pemilik'])->group(function () {
            // Void/Hapus Transaksi (Khusus Pemilik)
            Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
        });
        
    }); // End of Auth Middleware
}); // End of SQLiDefense Middleware