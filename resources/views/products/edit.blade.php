@extends('layouts.app')

@section('page-title', 'Edit Produk')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200 dark:bg-dark-card dark:border-gray-800/50 dark:shadow-brand-blue/5">
        
        <div class="mb-8 border-b border-gray-200 pb-4 dark:border-gray-800">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Produk</h2>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi detail produk di bawah ini.</p>
        </div>

        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="md:col-span-1">
                    <label for="sku" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kode Unik (SKU)</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" 
                        class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all placeholder-gray-400 font-mono dark:bg-dark-bg dark:border-gray-700 dark:text-white dark:placeholder-gray-600" 
                        required>
                    @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Produk</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                        class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all placeholder-gray-400 dark:bg-dark-bg dark:border-gray-700 dark:text-white dark:placeholder-gray-600" 
                        required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="category_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kategori</label>
                    <div class="relative">
                        <select name="category_id" id="category_id" 
                            class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all cursor-pointer dark:bg-dark-bg dark:border-gray-700 dark:text-white">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
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
                    <label for="supplier_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Supplier</label>
                    <div class="relative">
                        <select name="supplier_id" id="supplier_id" 
                            class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all cursor-pointer dark:bg-dark-bg dark:border-gray-700 dark:text-white">
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $product->supplier_id == $supplier->id ? 'selected' : '' }}>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="stock" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Stok Saat Ini</label>
                    <div class="relative">
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" 
                            class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl pl-4 pr-10 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all placeholder-gray-400 dark:bg-dark-bg dark:border-gray-700 dark:text-white dark:placeholder-gray-600" 
                            required min="0">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 text-xs dark:text-gray-500">Pcs</div>
                    </div>
                </div>

                <div>
                    <label for="price" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Harga Jual</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 font-bold dark:text-gray-500">Rp</div>
                        <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" 
                            class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all font-medium text-brand-green placeholder-gray-400 dark:bg-dark-bg dark:border-gray-700 dark:text-brand-green dark:placeholder-gray-600" 
                            required min="0">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-all text-sm font-medium dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                    Batal
                </a>
                <button type="submit" class="bg-brand-blue hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-brand-blue/20 transition-all transform active:scale-95 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection