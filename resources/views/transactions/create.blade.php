@extends('layouts.app')

@section('page-title', 'Transaksi Penjualan')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    
    <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200 dark:bg-dark-card dark:border-gray-800/50">
        <div class="mb-8 border-b border-gray-200 pb-4 dark:border-gray-800">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Kasir / Transaksi Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Masukan produk ke keranjang untuk memproses penjualan.</p>
        </div>

        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf
            
            <div id="transaction-items-container" class="space-y-4">
                <div class="grid grid-cols-12 gap-4 items-end transaction-item bg-gray-50 p-4 rounded-xl border border-gray-200 dark:bg-dark-bg/50 dark:border-gray-800/50">
                    
                    <div class="col-span-12 md:col-span-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pilih Produk</label>
                        <select name="product_id[]" class="product-select w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all cursor-pointer dark:bg-dark-bg dark:border-gray-700 dark:text-white" required onchange="updatePrice(this)">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} (Stok: {{ $product->stock }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-6 md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Qty</label>
                        <input type="number" name="quantity[]" class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all text-center dark:bg-dark-bg dark:border-gray-700 dark:text-white" required min="1" value="1" oninput="updateTotal()">
                    </div>

                    <div class="col-span-6 md:col-span-4">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Subtotal</label>
                        <input type="text" name="item_price[]" class="w-full bg-gray-100 border border-gray-300 text-brand-blue font-bold rounded-xl px-4 py-3 focus:outline-none cursor-not-allowed dark:bg-gray-800/50 dark:border-gray-700 dark:text-brand-green" readonly placeholder="Rp 0">
                    </div>

                    <div class="col-span-12 md:col-span-1 flex justify-end md:justify-center">
                        <button type="button" class="remove-item-btn w-full md:w-auto bg-red-100 text-red-500 hover:bg-red-500 hover:text-white border border-red-200 p-3 rounded-xl transition-all duration-200 flex items-center justify-center dark:bg-red-500/10 dark:border-red-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center my-6">
                <button type="button" id="add-item-btn" class="flex items-center gap-2 px-6 py-3 rounded-xl border border-dashed border-gray-400 text-gray-500 hover:text-brand-blue hover:border-brand-blue hover:bg-blue-50 transition-all duration-200 w-full md:w-auto justify-center dark:border-gray-600 dark:text-gray-400 dark:hover:text-white dark:hover:bg-brand-blue/10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Baris Produk</span>
                </button>
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center gap-6 dark:border-gray-800">
                <div class="text-center md:text-left">
                    <span class="block text-sm text-gray-500 uppercase tracking-wider mb-1">Total Pembayaran</span>
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white"><span id="grand-total" class="text-brand-blue dark:text-brand-green">Rp 0</span></h3>
                </div>
                <button type="submit" class="w-full md:w-auto bg-brand-blue hover:bg-blue-600 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-brand-blue/20 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Selesaikan Transaksi
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden dark:bg-dark-card dark:border-gray-800/50">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center dark:border-gray-800/50">
            <div>
                <h3 class="text-gray-800 font-semibold text-lg dark:text-white">Riwayat Transaksi Terakhir</h3>
                <p class="text-gray-500 text-xs mt-1">5 transaksi terakhir yang berhasil diproses.</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-xs bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-200 transition dark:bg-dark-bg dark:text-gray-400 dark:border-gray-700 dark:hover:text-white dark:hover:bg-gray-800">
                Lihat Semua Laporan
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase dark:bg-dark-bg dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-medium">Invoice</th>
                        <th class="px-6 py-4 font-medium">Tanggal</th>
                        <th class="px-6 py-4 font-medium text-right">Total</th>
                        <th class="px-6 py-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-600 text-sm dark:divide-gray-800/50 dark:text-gray-300">
                    @forelse ($recentTransactions as $trx)
                    <tr class="hover:bg-gray-50 transition-colors group dark:hover:bg-dark-hover">
                        <td class="px-6 py-4">
                            <span class="font-mono text-brand-blue bg-blue-50 px-2 py-1 rounded text-xs dark:bg-brand-blue/10">
                                {{ $trx->invoice_number }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 font-bold text-gray-800 text-right dark:text-white">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('transactions.show', ['transaction' => $trx->id, 'from' => 'transaction']) }}" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-400 hover:bg-brand-blue hover:text-white transition-all dark:bg-gray-800" 
                               title="Lihat Detail & Cetak">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada riwayat transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('transaction-items-container');
        const addButton = document.getElementById('add-item-btn');
        let itemIndex = 1;

        window.updatePrice = function(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price);
            const row = selectElement.closest('.transaction-item');
            const quantityInput = row.querySelector('input[name="quantity[]"]');
            const itemPriceInput = row.querySelector('input[name="item_price[]"]');
            
            if (!isNaN(price) && quantityInput) {
                const quantity = parseInt(quantityInput.value) || 0;
                const itemPrice = price * quantity;
                itemPriceInput.value = itemPrice.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
            } else {
                itemPriceInput.value = '';
            }
            updateTotal();
        }

        window.updateTotal = function() {
            let grandTotal = 0;
            const items = document.querySelectorAll('.transaction-item');
            items.forEach(item => {
                const selectElement = item.querySelector('.product-select');
                const quantityInput = item.querySelector('input[name="quantity[]"]');
                if(selectElement && quantityInput) {
                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                    const price = parseFloat(selectedOption.dataset.price);
                    const quantity = parseInt(quantityInput.value) || 0;
                    if (!isNaN(price) && !isNaN(quantity)) {
                        grandTotal += price * quantity;
                    }
                }
            });
            document.getElementById('grand-total').innerText = grandTotal.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
        }

        addButton.addEventListener('click', function () {
            const newItem = document.createElement('div');
            // CLASS DIBAWAH INI SANGAT PENTING: Harus sama dengan statis di atas agar style-nya konsisten
            newItem.className = 'grid grid-cols-12 gap-4 items-end transaction-item bg-gray-50 p-4 rounded-xl border border-gray-200 animate-fade-in-up dark:bg-dark-bg/50 dark:border-gray-800/50';
            
            newItem.innerHTML = `
                <div class="col-span-12 md:col-span-5">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pilih Produk</label>
                    <select name="product_id[]" class="product-select w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all cursor-pointer dark:bg-dark-bg dark:border-gray-700 dark:text-white" required onchange="updatePrice(this)">
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} (Stok: {{ $product->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-6 md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Qty</label>
                    <input type="number" name="quantity[]" class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all text-center dark:bg-dark-bg dark:border-gray-700 dark:text-white" required min="1" value="1" oninput="updateTotal()">
                </div>
                <div class="col-span-6 md:col-span-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Subtotal</label>
                    <input type="text" name="item_price[]" class="w-full bg-gray-100 border border-gray-300 text-brand-blue font-bold rounded-xl px-4 py-3 focus:outline-none cursor-not-allowed dark:bg-gray-800/50 dark:border-gray-700 dark:text-brand-green" readonly placeholder="Rp 0">
                </div>
                <div class="col-span-12 md:col-span-1 flex justify-end md:justify-center">
                    <button type="button" class="remove-item-btn w-full md:w-auto bg-red-100 text-red-500 hover:bg-red-500 hover:text-white border border-red-200 p-3 rounded-xl transition-all duration-200 flex items-center justify-center dark:bg-red-500/10 dark:border-red-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            `;
            container.appendChild(newItem);
            itemIndex++;
        });

        container.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-item-btn');
            if (btn) {
                const row = btn.closest('.transaction-item');
                row.remove();
                updateTotal();
            }
        });

        updateTotal();
    });
</script>

<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
</style>
@endsection