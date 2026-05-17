@extends('layouts.app')

@section('page-title', 'Manajemen Produk')

@section('content')
<div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 dark:bg-dark-card dark:border-gray-800/50 dark:shadow-brand-blue/5">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Daftar Produk</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola stok dan harga produk Anda di sini.</p>
        </div>
        
        <a href="{{ route('products.create') }}" class="bg-brand-blue text-white px-5 py-2.5 rounded-xl hover:bg-blue-600 transition-all duration-200 shadow-lg shadow-brand-blue/20 flex items-center gap-2 group">
            <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Produk Baru</span>
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800/50">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-50 text-left dark:bg-dark-bg">
                    <th class="px-5 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">SKU</th>
                    <th class="px-5 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Nama Produk</th>
                    <th class="px-5 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Kategori</th>
                    <th class="px-5 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Stok</th>
                    <th class="px-5 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Harga</th>
                    <th class="px-5 py-4 border-b border-gray-200 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800/50">
                @forelse ($products as $product)
                <tr class="hover:bg-gray-50 transition-colors duration-150 group dark:hover:bg-dark-hover">
                    <td class="px-5 py-4 text-sm whitespace-nowrap">
                        <span class="px-2 py-1 rounded text-xs font-mono border bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700">
                            {{ $product->sku }}
                        </span>
                    </td>
                    
                    <td class="px-5 py-4 text-sm">
                        <div class="flex items-center">
                            <div class="ml-3">
                                <p class="font-medium text-gray-800 dark:text-white">
                                    {{ $product->name }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td class="px-5 py-4 text-sm">
                        <span class="text-gray-500 dark:text-gray-400">{{ $product->category->name }}</span>
                    </td>

                    <td class="px-5 py-4 text-sm">
                        @if($product->stock <= 10)
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                </span>
                                <span class="font-bold text-red-600 dark:text-red-400 animate-pulse whitespace-nowrap">
                                    {{ $product->stock }} (Low)
                                </span>
                            </div>

                        @elseif($product->stock <= 50)
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-orange-400 animate-pulse"></span>
                                <span class="font-medium text-orange-600 dark:text-orange-400 whitespace-nowrap">
                                    {{ $product->stock }} (Med)
                                </span>
                            </div>

                        @else
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-medium text-green-600 dark:text-green-400">
                                    {{ $product->stock }}
                                </span>
                            </div>
                        @endif
                    </td>

                    <td class="px-5 py-4 text-sm font-medium text-brand-green whitespace-nowrap">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>

                    <td class="px-5 py-4 text-sm text-right">
                        <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-70 md:group-hover:opacity-100 transition-opacity">
                            
                            @if(auth()->user()->hasAnyRole(['admin_gudang', 'pemilik']))
                            <button onclick="openRestockModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock }})" 
                                class="p-2 rounded-lg transition-all bg-green-50 text-green-600 hover:bg-green-100 dark:bg-green-500/10 dark:text-green-400 dark:hover:bg-green-500 dark:hover:text-white"
                                title="Atur Stok">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </button>
                            @endif

                            @if(auth()->user()->hasRole('pemilik'))
                                <a href="{{ route('products.edit', $product) }}" class="p-2 rounded-lg transition-all bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white" title="Edit Data Master">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>

                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg transition-all bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white" title="Hapus Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p>Belum ada produk yang ditambahkan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>

<div id="restockModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" onclick="closeRestockModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full dark:bg-dark-card dark:border dark:border-gray-700">
            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-dark-card">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10 dark:bg-green-900/30">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Atur Stok: <span id="modalProductName" class="text-brand-blue"></span>
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Stok saat ini: <span id="modalCurrentStock" class="font-bold text-gray-800 dark:text-white"></span>
                                </p>
                                <input type="hidden" name="product_id" id="modalProductId">
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Mutasi</label>
                                    <select name="type" id="mutationType" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-brand-blue focus:border-brand-blue sm:text-sm rounded-md dark:bg-dark-bg dark:border-gray-600 dark:text-white" onchange="toggleSupplierInput()">
                                        <option value="in">Barang Masuk (Restock)</option>
                                        <option value="out">Barang Keluar (Rusak/Hilang)</option>
                                        <option value="opname">Opname (Koreksi Stok)</option>
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah (Qty)</label>
                                    <input type="number" name="quantity" min="1" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-brand-blue focus:border-brand-blue sm:text-sm dark:bg-dark-bg dark:border-gray-600 dark:text-white">
                                </div>
                                <div class="mt-4" id="supplierInputDiv">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                    <select name="supplier_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-brand-blue focus:border-brand-blue sm:text-sm rounded-md dark:bg-dark-bg dark:border-gray-600 dark:text-white">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach(\App\Models\Supplier::all() as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keterangan (Opsional)</label>
                                    <textarea name="description" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-brand-blue focus:border-brand-blue sm:text-sm dark:bg-dark-bg dark:border-gray-600 dark:text-white" placeholder="Contoh: Barang retur, stok opname bulanan..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-800/50">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-brand-blue text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-blue sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan Perubahan
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-dark-bg dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700" onclick="closeRestockModal()">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openRestockModal(id, name, stock) {
        document.getElementById('modalProductId').value = id;
        document.getElementById('modalProductName').innerText = name;
        document.getElementById('modalCurrentStock').innerText = stock;
        document.getElementById('restockModal').classList.remove('hidden');
    }

    function closeRestockModal() {
        document.getElementById('restockModal').classList.add('hidden');
    }

    function toggleSupplierInput() {
        const type = document.getElementById('mutationType').value;
        const supplierDiv = document.getElementById('supplierInputDiv');
        if (type === 'in') {
            supplierDiv.classList.remove('hidden');
        } else {
            supplierDiv.classList.add('hidden');
        }
    }
</script>
@endsection