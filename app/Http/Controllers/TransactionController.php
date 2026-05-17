<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        // Sesuaikan permission dengan role yang ada
        $this->middleware('permission:manage sales|access pos');
    }

    /**
     * Menampilkan Daftar Transaksi (History)
     * - Kasir: Hanya transaksi miliknya (dan sesi aktif)
     * - Admin/Pemilik: Semua transaksi
     */
    public function index()
    {
        $query = Transaction::with(['user', 'items']);

        if (auth()->user()->hasRole('kasir')) {
            $query->where('user_id', auth()->id())
                  ->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $transactions = $query->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Menampilkan Halaman Manual Transaction (Opsional, jika kasir input tanpa POS UI)
     */
    public function create()
    {
        $products = Product::where('stock', '>', 0)->get(); 
        $recentTransactions = Transaction::with('user')->latest()->take(5)->get();

        return view('transactions.create', compact('products', 'recentTransactions'));
    }

    /**
     * Menyimpan Transaksi Manual (Non-POS AJAX)
     * Tetap dipertahankan untuk backup jika POS error/butuh input manual admin.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id'   => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity'     => 'required|array',
            'quantity.*'   => 'required|integer|min:1',
        ]);

        if (count($validatedData['product_id']) !== count($validatedData['quantity'])) {
            return back()->with('error', 'Jumlah produk dan kuantitas tidak sesuai.');
        }

        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'invoice_number' => 'INV-' . time(),
                'user_id' => auth()->id(), // Mencatat siapa yang input
                'total_amount' => 0, // Akan diupdate di bawah
                'amount_paid' => 0, // Manual dianggap lunas/sesuai total
                'change' => 0,
            ]);

            $totalPrice = 0;

            foreach ($validatedData['product_id'] as $key => $productId) {
                $quantity = $validatedData['quantity'][$key];

                // Lock & Update Stok
                $product = Product::where('id', $productId)->lockForUpdate()->first();

                if ($product->stock < $quantity) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi.");
                }

                $product->decrement('stock', $quantity);

                $itemPrice = $product->price * $quantity;
                $totalPrice += $itemPrice;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $itemPrice
                ]);
            }

            // Update Total & Pembayaran Default (Cash)
            $transaction->update([
                'total_amount' => $totalPrice,
                'amount_paid' => $totalPrice, // Asumsi bayar pas
            ]);

            // Buat Payment Record Default (Cash)
            \App\Models\TransactionPayment::create([
                'transaction_id' => $transaction->id,
                'payment_method' => 'CASH',
                'amount_paid' => $totalPrice,
            ]);

            DB::commit();

            // Redirect langsung ke halaman struk HTML
            return redirect()->route('transactions.invoice', $transaction->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan Detail Transaksi (Untuk Admin Dashboard/History)
     */
    public function show($id)
    {
        $transaction = Transaction::with(['items.product', 'user', 'payments'])->findOrFail($id);
        
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Menampilkan Struk Thermal (HTML)
     * Diakses oleh POS Javascript via window.open()
     */
    public function invoice($id)
    {
        // Load semua relasi yang dibutuhkan di invoice.blade.php
        $transaction = Transaction::with([
            'items.product', 
            'user',      // Untuk nama kasir
            'payments'   // Untuk rincian split payment
        ])->findOrFail($id);
        
        // Return VIEW biasa (bukan PDF stream) agar bisa di-print browser
        return view('transactions.invoice', compact('transaction'));
    }

    /**
     * VOID / HAPUS Transaksi
     * - Mengembalikan stok
     * - Menghapus detail, payment, dan header transaksi
     */
    public function destroy($id)
    {
        // Pastikan hanya Pemilik (atau role yg diizinkan di middleware) yang bisa akses
        // Tapi sudah diproteksi middleware di web.php

        $transaction = Transaction::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            // 1. Restore Stok
            foreach ($transaction->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            // 2. Hapus Item & Payment (Cascade biasanya handle ini, tapi manual lebih aman)
            $transaction->items()->delete();
            $transaction->payments()->delete();

            // 3. Hapus Transaksi
            $transaction->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Transaksi berhasil dihapus (Void). Stok telah dikembalikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}