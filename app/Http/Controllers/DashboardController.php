<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Supplier;
use App\Models\InventoryLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // --- 1. SETUP WAKTU (SHARED) ---
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $lastMonth = Carbon::now()->subMonth()->month;
        $lastMonthYear = Carbon::now()->subMonth()->year;

        // --- 2. DATA UMUM (Bisa dilihat Semua Role) ---
        $totalProducts = Product::count();
        $newProductsThisMonth = Product::whereMonth('created_at', $currentMonth)
                                        ->whereYear('created_at', $currentYear)
                                        ->count();

        // Data Grafik Stok (Pie Chart)
        $stockAvailable = Product::where('stock', '>', 10)->count();
        $stockLow = Product::where('stock', '<=', 10)->where('stock', '>', 0)->count();
        $stockOut = Product::where('stock', 0)->count();
        $stockChartData = [$stockAvailable, $stockLow, $stockOut];

        // --- 3. INISIALISASI VARIABLE DEFAULT ---
        $salesThisMonth = 0;
        $salesGrowth = 0;
        $totalTransactions = 0;
        $recentTransactions = [];
        $salesChartData = [];
        
        $totalSuppliers = 0;
        $lowStockProducts = [];
        $recentMutations = [];
        $myTransactionsToday = 0;
        $myTransactionCount = 0; // For kasir sidebar
        $hourlySales = []; 
        $topSellingData = []; // Default value to prevent error for other roles
        
        // Admin Gudang Variables (Default 0/Null)
        $barangMasuk = 0;
        $barangKeluar = 0;
        $lowStock = 0;

        // --- 4. LOGIKA BERDASARKAN ROLE ---

        // A. JIKA PEMILIK (Akses Full Data Keuangan)
        if (auth()->user()->hasRole('pemilik')) {
            
            $salesThisMonth = Transaction::whereMonth('created_at', $currentMonth)
                                         ->whereYear('created_at', $currentYear)
                                         ->sum('total_amount');
            
            $salesLastMonth = Transaction::whereMonth('created_at', $lastMonth)
                                         ->whereYear('created_at', $lastMonthYear)
                                         ->sum('total_amount');

            if ($salesLastMonth > 0) {
                $salesGrowth = (($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100;
            } elseif ($salesThisMonth > 0) {
                $salesGrowth = 100; 
            }

            $totalTransactions = Transaction::count();
            $recentTransactions = Transaction::with('items')->latest()->take(5)->get();

            // Grafik Penjualan
            $salesData = Transaction::select(
                DB::raw('SUM(total_amount) as total'),
                DB::raw('MONTH(created_at) as month')
            )
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            $monthlySales = array_fill(1, 12, 0);
            foreach ($salesData as $data) {
                $monthlySales[$data->month] = $data->total;
            }

            $salesChartData = [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'values' => array_values($monthlySales)
            ];
        } 
        
        // B. JIKA KASIR (Akses Data Penjualan Sendiri)
        elseif (auth()->user()->hasRole('kasir')) {
            // Hitung berapa kali dia melayani pelanggan hari ini
            $myTransactionsToday = Transaction::where('user_id', auth()->id())
                                              ->whereDate('created_at', Carbon::today())
                                              ->count();
            
            // Hitung total nominal penjualan dia hari ini
            $mySalesToday = Transaction::where('user_id', auth()->id())
                                              ->whereDate('created_at', Carbon::today())
                                              ->sum('total_amount');
            
            // For sidebar display
            $myTransactionCount = $myTransactionsToday;
            
            // Kasir juga perlu lihat stok menipis (biar bisa lapor)
            $lowStockProducts = Product::where('stock', '<=', 10)
                                       ->orderBy('stock', 'asc')
                                       ->take(5)
                                       ->get();
                                       
            // Grafik Penjualan Barang Terbanyak (Top 5 Items)
            $topItems = DB::table('transaction_items')
                        ->join('products', 'transaction_items.product_id', '=', 'products.id')
                        ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                        ->whereMonth('transactions.created_at', $currentMonth)
                        ->whereYear('transactions.created_at', $currentYear)
                        ->select('products.name', DB::raw('SUM(transaction_items.quantity) as total_qty'))
                        ->groupBy('products.id', 'products.name')
                        ->orderByDesc('total_qty')
                        ->limit(5)
                        ->get();

            $topSellingData = [
                'labels' => $topItems->pluck('name')->toArray(),
                'values' => $topItems->pluck('total_qty')->toArray()
            ];

            // If empty, provide some dummy data to avoid broken chart
            if (empty($topSellingData['labels'])) {
                 $topSellingData = [
                    'labels' => ['Belum ada transaksi'],
                    'values' => [0]
                ];
            }
        }

        // C. JIKA ADMIN GUDANG (Akses Stok & Supplier)
        else {
            // 1. Barang Masuk Hari Ini
            $barangMasuk = InventoryLog::where('type', 'in')
                                       ->whereDate('created_at', Carbon::today())
                                       ->sum('quantity');

            // 2. Barang Keluar Hari Ini
            $barangKeluar = InventoryLog::where('type', 'out')
                                        ->whereDate('created_at', Carbon::today())
                                        ->sum('quantity');

            // 3. Low Stock Count
            $lowStock = Product::where('stock', '<=', 10)->count(); // Alert threshold 10

            // 4. Mutasi Terakhir
            $recentMutations = InventoryLog::with(['product', 'user'])
                                           ->latest()
                                           ->take(5)
                                           ->get();
            
            // 5. Data untuk Grafik Top Low Stock
            $topLowStock = Product::where('stock', '>', 0)
                                  ->where('stock', '<=', 20)
                                  ->orderBy('stock', 'asc')
                                  ->take(5)
                                  ->get();

            if ($topLowStock->isNotEmpty()) {
                $lowStockData['labels'] = $topLowStock->pluck('name')->toArray();
                $lowStockData['values'] = $topLowStock->pluck('stock')->toArray();
            } else {
                $lowStockData = [
                    'labels' => ['Produk A', 'Produk B', 'Produk C', 'Produk D', 'Produk E'],
                    'values' => [5, 8, 12, 15, 18]
                ];
            }
        }

        // --- 5. PACKING DATA ---
        $stats = [
            'total_products' => $totalProducts,
            'new_products_this_month' => $newProductsThisMonth,
            'sales_this_month' => $salesThisMonth,
            'sales_growth' => round($salesGrowth, 1),
            'total_transactions' => $totalTransactions,
            
            // Statistik Khusus Kasir
            'my_transactions_today' => $myTransactionsToday, 
            'my_sales_today' => $mySalesToday ?? 0, 
        ];

        // --- 6. NEW: DATA FOR INVENTORY CHARTS ---
        $categoryData = ['labels' => [], 'values' => []];
        $lowStockData = ['labels' => [], 'values' => []];

        // Only for admin_gudang, pemilik, and pekerja_gudang
        if (auth()->user()->hasAnyRole(['admin_gudang', 'pemilik', 'pekerja_gudang'])) {
            // Category Distribution Data - Using JOIN with categories table
            $categoryStats = Product::select('categories.name as category_name', DB::raw('count(*) as total'))
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->groupBy('categories.id', 'categories.name')
                ->get();
            
            if ($categoryStats->isNotEmpty()) {
                $categoryData['labels'] = $categoryStats->pluck('category_name')->toArray();
                $categoryData['values'] = $categoryStats->pluck('total')->toArray();
            } else {
                // Fallback dummy data
                $categoryData = [
                    'labels' => ['Sembako', 'Minuman', 'Snack', 'Lainnya'],
                    'values' => [50, 30, 15, 5]
                ];
            }

            // Top 5 Low Stock Products Data
            $topLowStock = Product::where('stock', '>', 0)
                ->where('stock', '<=', 20)
                ->orderBy('stock', 'asc')
                ->take(5)
                ->get();
            
            if ($topLowStock->isNotEmpty()) {
                $lowStockData['labels'] = $topLowStock->pluck('name')->toArray();
                $lowStockData['values'] = $topLowStock->pluck('stock')->toArray();
            } else {
                // Fallback dummy data
                $lowStockData = [
                    'labels' => ['Produk A', 'Produk B', 'Produk C', 'Produk D', 'Produk E'],
                    'values' => [5, 8, 12, 15, 18]
                ];
            }
        }

        return view('dashboard', compact(
            'stats', 
            'stockChartData', 
            'recentTransactions', 
            'salesChartData',
            'topSellingData', // Changed from hourlySales
            'totalSuppliers',
            'lowStockProducts',
            'recentMutations',
            'categoryData',      // NEW
            'lowStockData',      // NEW
            'myTransactionCount', // For kasir sidebar
            'barangMasuk',        // Admin Gudang
            'barangKeluar',       // Admin Gudang
            'lowStock'            // Admin Gudang
        ));
    }
}