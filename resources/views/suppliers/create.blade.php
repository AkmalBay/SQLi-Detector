@extends('layouts.app')

@section('page-title', 'Tambah Supplier Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200 dark:bg-dark-card dark:border-gray-800/50 dark:shadow-brand-blue/5">
        
        <div class="mb-8 border-b border-gray-200 pb-4 dark:border-gray-800">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah Supplier Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Daftarkan pemasok baru untuk melengkapi inventaris toko Anda.</p>
        </div>

        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Supplier</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                        class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all placeholder-gray-400 dark:bg-dark-bg dark:border-gray-700 dark:text-white dark:placeholder-gray-600" 
                        placeholder="Contoh: PT. Sembako Makmur" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="contact_person" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Kontak (CP)</label>
                    <div class="relative">
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" 
                            class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all placeholder-gray-400 dark:bg-dark-bg dark:border-gray-700 dark:text-white dark:placeholder-gray-600"
                            placeholder="Contoh: Bpk. Budi">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none dark:text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label for="phone" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nomor Telepon / WhatsApp</label>
                <div class="relative">
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                        class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all placeholder-gray-400 dark:bg-dark-bg dark:border-gray-700 dark:text-white dark:placeholder-gray-600"
                        placeholder="0812...">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none dark:text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <label for="address" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Alamat Lengkap</label>
                <textarea name="address" id="address" rows="3" 
                    class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all placeholder-gray-400 resize-none dark:bg-dark-bg dark:border-gray-700 dark:text-white dark:placeholder-gray-600"
                    placeholder="Masukan alamat lengkap supplier...">{{ old('address') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ route('suppliers.index') }}" class="px-6 py-3 rounded-xl text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-all text-sm font-medium dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                    Batal
                </a>
                <button type="submit" class="bg-brand-blue hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-brand-blue/20 transition-all transform active:scale-95 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Simpan Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection