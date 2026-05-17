<?php

namespace App\Http\Controllers;

use App\Models\PosShift;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\TransactionItem;
use App\Models\InventoryLog;
use App\Models\CartHold; // PERLU DITAMBAHKAN: Model untuk Hold Cart
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:access pos');
    }

    /**
     * Tampilkan Halaman Utama POS.
     */
    public function index()
    {
        $user = Auth::user();
        
        // 1. Cek Shift Aktif
        $activeShift = PosShift::where('user_id', $user->id)
            ->where('status', 'OPEN')
            ->first();

        if (!$activeShift) {
            return view('pos.open_shift');
        }

        // 2. Load Produk
        $products = Product::select('id', 'sku', 'name', 'price', 'stock')->get();

        // 3. BARU: Load Data Hold Cart milik kasir ini
        $heldCarts = CartHold::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pos.index', compact('activeShift', 'products', 'heldCarts'));
    }

    // --- Manajemen Shift (Sama seperti kode Anda) ---

    public function openShiftForm()
    {
        $activeShift = PosShift::where('user_id', Auth::id())->where('status', 'OPEN')->first();
        if ($activeShift) {
            return redirect()->route('pos.index');
        }
        return view('pos.open_shift');
    }

    public function openShift(Request $request)
    {
        $request->validate(['starting_cash' => 'required|integer|min:0']);

        $activeShift = PosShift::where('user_id', Auth::id())->where('status', 'OPEN')->first();

        if ($activeShift) {
            return redirect()->route('pos.index')->with('error', 'Anda sudah memiliki shift yang aktif.');
        }

        PosShift::create([
            'user_id' => Auth::id(),
            'start_time' => now(),
            'starting_cash' => $request->starting_cash,
            'status' => 'OPEN',
        ]);

        return redirect()->route('pos.index')->with('success', 'Shift berhasil dibuka.');
    }

    public function closeShiftForm()
    {
        $activeShift = PosShift::where('user_id', Auth::id())->where('status', 'OPEN')->first();

        if (!$activeShift) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada shift aktif. Silakan buka shift terlebih dahulu.');
        }

        $cashPayments = TransactionPayment::where('pos_shift_id', $activeShift->id)
            ->where('payment_method', 'CASH')
            ->sum('amount_paid');
            
        $nonCashPayments = TransactionPayment::where('pos_shift_id', $activeShift->id)
            ->where('payment_method', '!=', 'CASH')
            ->sum('amount_paid');
            
        $expectedCash = $activeShift->starting_cash + $cashPayments;

        return view('pos.close_shift', compact('activeShift', 'expectedCash', 'nonCashPayments'));
    }

    public function closeShift(Request $request, PosShift $shift)
    {
        $request->validate([
            'actual_cash' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $cashPayments = TransactionPayment::where('pos_shift_id', $shift->id)
            ->where('payment_method', 'CASH')
            ->sum('amount_paid');
            
        $nonCashPayments = TransactionPayment::where('pos_shift_id', $shift->id)
            ->where('payment_method', '!=', 'CASH')
            ->sum('amount_paid');
            
        $expectedCash = $shift->starting_cash + $cashPayments;
        
        $shift->update([
            'end_time' => now(),
            'expected_cash' => $expectedCash,
            'actual_cash' => $request->actual_cash,
            'difference' => $request->actual_cash - $expectedCash,
            'status' => 'CLOSED',
            'notes' => $request->notes,
        ]);

        return view('pos.shift_summary', ['shift' => $shift, 'nonCashPayments' => $nonCashPayments]);
    }

    // --- AJAX Produk (Sama seperti kode Anda) ---

    public function searchProduct(Request $request)
    {
        $keyword = $request->query('q');
        // Sanitize keyword untuk mencegah injection dan XSS
        $keyword = htmlspecialchars(strip_tags($keyword), ENT_QUOTES, 'UTF-8');

        $products = Product::where('sku', 'like', "%{$keyword}%")
            ->orWhere('name', 'like', "%{$keyword}%")
            ->select('id', 'sku', 'name', 'price', 'stock')
            ->take(10)
            ->get();
        return response()->json($products);
    }

    public function checkStock(Product $product, Request $request)
    {
        $quantity = $request->input('quantity', 1);
        if ($product->stock < $quantity) {
            return response()->json([
                'available' => false, 
                'message' => 'Stok sisa ' . $product->stock
            ], 400); 
        }
        return response()->json(['available' => true, 'product' => $product]);
    }

    // --- FITUR BARU: HOLD CART ---

    public function holdCart(Request $request)
    {
        $request->validate([
            'cart_items' => 'required', // Bisa array atau JSON string, tergantung kiriman FE
            'reference_name' => 'nullable|string',
            'total_amount' => 'required|numeric'
        ]);

        // Jika cart_items dikirim sebagai array, encode jadi JSON. Jika sudah JSON, biarkan.
        $itemsData = is_array($request->cart_items) ? json_encode($request->cart_items) : $request->cart_items;

        CartHold::create([
            'user_id' => Auth::id(),
            'reference_name' => $request->reference_name ?? 'Pelanggan ' . date('H:i'),
            'items' => json_decode($itemsData), // Pastikan disimpan sebagai array/json yang valid di model
            'total_amount' => $request->total_amount,
            'note' => $request->note ?? null
        ]);

        return response()->json(['success' => true, 'message' => 'Pesanan disimpan sementara (Hold).']);
    }

    public function restoreCart($id)
    {
        // Pastikan hanya bisa mengambil hold cart milik user yang sedang login
        $heldCart = CartHold::where('user_id', Auth::id())->findOrFail($id);
        
        $data = $heldCart->toArray(); // Ambil datanya
        $heldCart->delete(); // Hapus dari database agar tidak duplikat

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function removeHeldCart($id)
    {
        CartHold::where('user_id', Auth::id())->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // --- Transaksi Utama (Dengan Strict Stock) ---

    public function storeTransaction(Request $request)
    {
        $user = Auth::user();
        $activeShift = PosShift::where('user_id', $user->id)->where('status', 'OPEN')->first();

        if (!$activeShift) {
            return response()->json(['error' => 'Shift belum dibuka.'], 403);
        }

        $validated = $request->validate([
            'total_amount' => 'required|numeric|min:1',
            'amount_paid' => 'required|numeric|min:1',
            'change' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'cart_items' => 'required|array',
            'cart_items.*.id' => 'required|exists:products,id',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'payments' => 'required|array', 
            'payments.*.method' => 'required|string', 
            'payments.*.amount' => 'required|numeric|min:1',
        ]);
        
        $totalPayments = collect($validated['payments'])->sum('amount');
        // Gunakan pembulatan atau toleransi kecil untuk float comparison jika perlu
        if ($totalPayments < $validated['total_amount']) {
             return response()->json(['error' => 'Jumlah pembayaran kurang.'], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Buat Header Transaksi dengan invoice number unik
            // Menggunakan uniqid() untuk mencegah duplikat invoice number
            $transaction = Transaction::create([
                'invoice_number' => 'INV-' . date('Ymd') . strtoupper(substr(uniqid(), -6)),
                'user_id' => $user->id,
                'pos_shift_id' => $activeShift->id,
                'customer_id' => $validated['customer_id'] ?? null,
                'total_amount' => $validated['total_amount'],
                'total_price' => $validated['total_amount'], // Sementara disamakan dengan total_amount (Subtotal)
                'amount_paid' => $validated['amount_paid'],
                'change' => $validated['change'],
            ]);

            // 2. Proses Item dengan STRICT LOCK
            foreach ($validated['cart_items'] as $item) {
                // UPDATE PENTING: Gunakan lockForUpdate()
                // Ini mencegah stok minus jika 2 kasir menekan bayar bersamaan
                $product = Product::where('id', $item['id'])->lockForUpdate()->first();

                // Cek stok lagi setelah di-lock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak cukup. Sisa: {$product->stock}");
                }

                // Simpan Detail Transaksi
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                // Kurangi Stok
                $product->decrement('stock', $item['quantity']);

                // Catat Log
                InventoryLog::create([
                    'product_id'  => $product->id,
                    'user_id'     => $user->id,
                    'type'        => 'out',
                    'quantity'    => $item['quantity'],
                    'description' => 'Penjualan #' . $transaction->invoice_number,
                ]);
            }

            // 3. Simpan Pembayaran
            foreach ($validated['payments'] as $payment) {
                TransactionPayment::create([
                    'transaction_id' => $transaction->id,
                    'pos_shift_id' => $activeShift->id,
                    'payment_method' => $payment['method'],
                    'amount_paid' => $payment['amount'],
                ]);
            }

        DB::commit();
            
        // BAGIAN SUKSES (BENAR)
        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil!',
            'transaction_id' => $transaction->id, // ID ini dikirim ke JS untuk buka link struk
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        // BAGIAN ERROR (PERBAIKAN)
        // Jangan return success=true, dan jangan panggil $transaction->id
        return response()->json([
            'success' => false, // Ubah jadi false
            'error' => 'Gagal memproses transaksi: ' . $e->getMessage(), // Tampilkan pesan error asli
        ], 400); // Beri status code 400 (Bad Request)
        }
    }
}