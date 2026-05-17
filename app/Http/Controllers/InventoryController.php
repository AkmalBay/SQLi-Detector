<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Menampilkan Halaman Riwayat Mutasi Stok (Inventory Log)
     */
    public function index()
    {
        // Ambil data log, urutkan dari yang terbaru
        $logs = InventoryLog::with(['product', 'user', 'supplier'])
                            ->latest()
                            ->paginate(15); 

        // Ambil data untuk dropdown di modal input stok
        $products  = Product::orderBy('name')->get();
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        return view('inventory.index', compact('logs', 'products', 'suppliers'));
    }

    /**
     * Handle simpan perubahan stok
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type'       => 'required|in:in,out,opname',
            'quantity'   => 'required|integer|min:1',
            'supplier_id'=> 'nullable|exists:suppliers,id',
            'description'=> 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::lockForUpdate()->findOrFail($request->product_id);

            // 2. Logika Perubahan Stok Produk Utama
            if ($request->type === 'in') {
                $product->increment('stock', $request->quantity);
            } elseif ($request->type === 'out') {
                if ($product->stock < $request->quantity) {
                    throw new \Exception("Stok tidak mencukupi! Sisa stok: " . $product->stock);
                }
                $product->decrement('stock', $request->quantity);
            } elseif ($request->type === 'opname') {
                $product->update(['stock' => $request->quantity]);
            }

            // 3. CATAT KE RIWAYAT (HAPUS IF CLASS EXISTS)
            // Kita paksa simpan sekarang.
            InventoryLog::create([
                'product_id' => $request->product_id,
                'user_id'    => auth()->id(),
                'supplier_id'=> $request->supplier_id,
                'type'       => $request->type,
                'quantity'   => $request->quantity,
                'description'=> $request->description,
            ]);

            DB::commit();
            return back()->with('success', 'Stok berhasil diperbarui dan tercatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update stok: ' . $e->getMessage());
        }
    }
}