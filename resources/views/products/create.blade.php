@extends('layouts.app')

@section('page-title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200 dark:bg-dark-card dark:border-gray-800/50 dark:shadow-brand-blue/5">
        
        <div class="mb-8 border-b border-gray-200 pb-4 dark:border-gray-800">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah Produk Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Isi formulir lengkap di bawah ini untuk menambahkan inventaris baru.</p>
        </div>

        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="md:col-span-1">
                    <label for="sku" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">Kode Unik (SKU)</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku') }}" 
                        class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all placeholder-gray-400 font-mono dark:bg-dark-bg dark:border-gray-700 dark:text-white dark:placeholder-gray-600" 
                        placeholder="CONTOH-001" required>
                    @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">Nama Produk</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                        class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all placeholder-gray-400 dark:bg-dark-bg dark:border-gray-700 dark:text-white dark:placeholder-gray-600" 
                        placeholder="Masukan nama produk..." required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="category_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">Kategori</label>
                    <div class="relative">
                        <select name="category_id" id="category_id" 
                            class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all cursor-pointer dark:bg-dark-bg dark:border-gray-700 dark:text-white">
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 dark:text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="supplier_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">Supplier</label>
                    <div class="relative">
                        <select name="supplier_id" id="supplier_id" 
                            class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all cursor-pointer dark:bg-dark-bg dark:border-gray-700 dark:text-white">
                            <option value="" disabled selected>Pilih Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 dark:text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 mb-8 dark:bg-brand-blue/10 dark:border-brand-blue/20">
                <div class="flex items-center gap-2 mb-4 text-brand-blue font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <h3 class="text-md uppercase tracking-wider">Satuan Dasar (Eceran)</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="stock" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">Stok Awal</label>
                        <div class="relative">
                            <input type="number" name="stock" id="stock" value="{{ old('stock') }}" 
                                class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl pl-4 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all placeholder-gray-400 dark:bg-dark-bg dark:border-gray-700 dark:text-white dark:placeholder-gray-600" 
                                placeholder="0" required min="0">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 text-xs dark:text-gray-500">Pcs</div>
                        </div>
                    </div>

                    <div>
                        <label for="price" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">Harga Jual (Eceran)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 font-bold dark:text-gray-500">Rp</div>
                            <input type="number" name="price" id="price" value="{{ old('price') }}" 
                                class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all font-medium text-brand-green placeholder-gray-400 dark:bg-dark-bg dark:border-gray-700 dark:text-brand-green dark:placeholder-gray-600" 
                                placeholder="0" required min="0">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 mb-8 dark:bg-white/5 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center gap-2 text-gray-700 dark:text-gray-200 font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <h3 class="text-md uppercase tracking-wider">Satuan Tambahan (Grosir)</h3>
                    </div>
                    <button type="button" onclick="addUnitRow()" class="text-xs bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-lg transition-all shadow-sm flex items-center gap-2 dark:bg-dark-card dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Satuan
                    </button>
                </div>
                
                <div id="unit-container" class="space-y-4">
                    </div>
                
                <p class="text-xs text-gray-400 mt-4 italic dark:text-gray-500">
                    *Contoh: Nama "Dus", Isi "40" Pcs, Harga "115000". Sistem otomatis mengkonversi stok saat penjualan.
                </p>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-all text-sm font-medium dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                    Batal
                </a>
                <button type="submit" class="bg-brand-blue hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-brand-blue/20 transition-all transform active:scale-95 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function addUnitRow() {
        const container = document.getElementById('unit-container');
        const index = container.children.length; // Index unik untuk array name
        
        const html = `
            <div class="grid grid-cols-12 gap-4 items-end p-4 bg-white rounded-xl border border-gray-200 shadow-sm relative group dark:bg-dark-bg dark:border-gray-700 animate-fade-in-down">
                <div class="col-span-4 md:col-span-3">
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Nama Satuan</label>
                    <input type="text" name="units[${index}][name]" placeholder="Cth: Dus" class="w-full text-sm rounded-lg border-gray-300 focus:ring-brand-blue focus:border-brand-blue dark:bg-dark-card dark:border-gray-600 dark:text-white" required>
                </div>
                <div class="col-span-3 md:col-span-3">
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Isi (Pcs)</label>
                    <input type="number" name="units[${index}][conversion]" placeholder="Cth: 40" class="w-full text-sm rounded-lg border-gray-300 focus:ring-brand-blue focus:border-brand-blue dark:bg-dark-card dark:border-gray-600 dark:text-white" required>
                </div>
                <div class="col-span-4 md:col-span-4">
                    <label class="text-[10px] uppercase font-bold text-gray-400 mb-1 block">Harga Jual (Rp)</label>
                    <input type="number" name="units[${index}][price]" placeholder="Cth: 115000" class="w-full text-sm rounded-lg border-gray-300 focus:ring-brand-blue focus:border-brand-blue dark:bg-dark-card dark:border-gray-600 dark:text-white" required>
                </div>
                <div class="col-span-1 md:col-span-2 flex justify-end">
                    <button type="button" onclick="this.closest('.grid').remove()" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors dark:hover:bg-red-900/20" title="Hapus Baris">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }
</script>

<style>
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down {
        animation: fadeInDown 0.3s ease-out;
    }
</style>
@endsection