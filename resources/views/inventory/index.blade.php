@extends('layouts.app')

@section('page-title', 'Riwayat Stok')

@section('content')
<div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 dark:bg-dark-card dark:border-gray-800/50">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-white">Log Mutasi Barang</h2>
            <p class="text-sm text-gray-500 mt-1">Riwayat keluar masuk barang (Bukan Transaksi Penjualan).</p>
        </div>
        
        @if(auth()->user()->hasAnyRole(['admin_gudang', 'pemilik']))
        <button onclick="openModal()" class="w-full md:w-auto bg-brand-blue text-white px-5 py-2.5 rounded-xl hover:bg-blue-600 transition-all shadow-lg shadow-brand-blue/20 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>Input Perubahan Stok</span>
        </button>
        @endif
    </div>

    <!-- ... existing table code ... -->
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800/50">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-50 text-left dark:bg-dark-bg">
                    <th class="px-5 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Tanggal</th>
                    <th class="px-5 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Produk</th>
                    <th class="px-5 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center dark:border-gray-800 dark:text-gray-400">Tipe</th>
                    <th class="px-5 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center dark:border-gray-800 dark:text-gray-400">Jumlah</th>
                    <th class="px-5 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Oleh</th>
                    <th class="px-5 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800/50">
                @forelse ($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors dark:hover:bg-dark-hover">
                    <td class="px-5 py-4 text-sm whitespace-nowrap text-gray-600 dark:text-gray-300">
                        {{ $log->created_at->format('d M Y, H:i') }}
                    </td>
                    
                    <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white">
                        {{ $log->product->name ?? 'Produk Terhapus' }}
                    </td>

                    <td class="px-5 py-4 text-sm text-center whitespace-nowrap">
                        @if($log->type == 'in')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                Masuk
                            </span>
                        @elseif($log->type == 'out')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                Keluar
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Opname
                            </span>
                        @endif
                    </td>

                    <td class="px-5 py-4 text-sm text-center font-bold text-gray-800 dark:text-white">
                        {{ $log->quantity }}
                    </td>

                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        {{ $log->user->name ?? 'System' }}
                    </td>

                    <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 italic">
                        {{ $log->description ?: '-' }}
                        @if($log->supplier)
                            <div class="text-xs text-brand-blue not-italic mt-0.5">
                                Supp: {{ $log->supplier->name }}
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                        Belum ada riwayat mutasi barang.
                        <button onclick="openModal()" class="text-brand-blue hover:underline font-medium ml-1">Input Sekarang</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>

<!-- Modal Input Stok -->
<div id="stockModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-dark-card border border-gray-200 dark:border-gray-700">
            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-dark-card">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10 dark:bg-blue-900/30">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Input Perubahan Stok
                            </h3>
                            <div class="mt-4 space-y-4">
                                
                                <div>
                                    <label for="product_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Produk</label>
                                    <select name="product_id" id="product_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-brand-blue focus:border-brand-blue sm:text-sm rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ $product->stock }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Perubahan</label>
                                    <select name="type" id="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-brand-blue focus:border-brand-blue sm:text-sm rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white" required>
                                        <option value="in">Barang Masuk (Restock)</option>
                                        <option value="out">Barang Keluar (Rusak/Expired/Lainnya)</option>
                                        <option value="opname">Stok Opname (Koreksi)</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah</label>
                                    <input type="number" name="quantity" id="quantity" min="1" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-blue focus:border-brand-blue sm:text-sm dark:bg-dark-bg dark:border-gray-600 dark:text-white" required placeholder="Contoh: 10">
                                </div>

                                <div id="supplier_field">
                                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier (Opsional)</label>
                                    <select name="supplier_id" id="supplier_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-brand-blue focus:border-brand-blue sm:text-sm rounded-lg dark:bg-dark-bg dark:border-gray-600 dark:text-white">
                                        <option value="">-- Tidak Ada --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan</label>
                                    <textarea name="description" id="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-blue focus:border-brand-blue sm:text-sm dark:bg-dark-bg dark:border-gray-600 dark:text-white" placeholder="Contoh: Barang baru datang..."></textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-dark-bg/50">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-brand-blue text-base font-medium text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        Simpan
                    </button>
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-dark-bg dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-800 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('stockModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('stockModal').classList.add('hidden');
    }

    // Optional: Auto-hide supplier field if not 'in'
    document.getElementById('type').addEventListener('change', function() {
        const type = this.value;
        const supplierField = document.getElementById('supplier_field');
        if (type === 'in') {
            supplierField.style.display = 'block';
        } else {
            supplierField.style.display = 'none'; // logic bisnis: biasanya keluar ga butuh supplier
        }
    });
</script>
@endsection