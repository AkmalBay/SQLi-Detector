<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\InventoryLog;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct()
    {
        // PERBAIKAN: Kecualikan 'index' (Daftar Produk) dan 'show' (Detail) dari batasan izin.
        // Artinya: Kasir bisa mengakses 'index' dan 'show'.
        // Izin 'manage products' hanya wajib untuk Tambah (create/store), Edit (edit/update), dan Hapus (destroy).
        $this->middleware('permission:manage products')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        // PERBAIKAN: Menambahkan logika Pencarian (Search)
        // Ini penting agar Kasir bisa mencari produk by Nama atau SKU dengan cepat.
        
        $query = Product::with(['category', 'supplier']);

        // Jika ada input 'search' dari URL (misal: ?search=kecap)
        if ($request->has('search')) {
            $keyword = $request->search;
            // Sanitize keyword untuk mencegah injection
            $keyword = htmlspecialchars(strip_tags($keyword), ENT_QUOTES, 'UTF-8');
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        // Tampilkan 10 data per halaman
        $products = $query->paginate(10);
        
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input Utama
        $validated = $request->validate([
            'sku' => 'required|unique:products,sku',
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // A. Simpan Produk Utama (Eceran)
            $product = Product::create($validated);

            // B. Simpan Multi Satuan (Grosir) - JIKA ADA
            if ($request->has('units')) {
                foreach ($request->units as $unit) {
                    if (isset($unit['name']) && isset($unit['conversion']) && isset($unit['price'])) {
                        ProductUnit::create([
                            'product_id' => $product->id,
                            'unit_name' => $unit['name'],
                            'conversion_factor' => $unit['conversion'],
                            'price' => $unit['price'],
                        ]);
                    }
                }
            }

            // C. Catat Log Stok Awal
            if ($product->stock > 0) {
                InventoryLog::create([
                    'product_id'  => $product->id,
                    'user_id'     => auth()->id(),
                    'supplier_id' => $product->supplier_id,
                    'type'        => 'in',
                    'quantity'    => $product->stock,
                    'description' => 'Stok Awal (Produk Baru)',
                ]);
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        // Load juga satuan tambahannya untuk ditampilkan di form edit
        $product->load('units'); 
        
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:products,sku,' . $product->id,
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $product, $validated) {
            $oldStock = $product->stock;
            $newStock = $validated['stock'];

            // Update Data Utama (termasuk Stok)
            $product->update($validated);

            // Catat log jika stok berubah
            if ($oldStock != $newStock) {
                $diff = $newStock - $oldStock;
                InventoryLog::create([
                    'product_id'  => $product->id,
                    'user_id'     => auth()->id(),
                    'supplier_id' => $product->supplier_id,
                    'type'        => $diff > 0 ? 'in' : 'out',
                    'quantity'    => abs($diff),
                    'description' => 'Koreksi stok via Edit Produk (dari ' . $oldStock . ' ke ' . $newStock . ')',
                ]);
            }

            // Update Multi Satuan
            // Hapus yang lama, buat ulang yang baru (paling aman & mudah untuk menghindari duplikasi/konflik ID)
            $product->units()->delete();

            if ($request->has('units')) {
                foreach ($request->units as $unit) {
                    if (isset($unit['name']) && isset($unit['conversion']) && isset($unit['price'])) {
                        ProductUnit::create([
                            'product_id' => $product->id,
                            'unit_name' => $unit['name'],
                            'conversion_factor' => $unit['conversion'],
                            'price' => $unit['price'],
                        ]);
                    }
                }
            }
        });

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}