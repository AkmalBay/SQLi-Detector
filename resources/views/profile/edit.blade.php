@extends('layouts.app')

@section('page-title', 'Kelola Akun')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-dark-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Profil Saya</h2>
        <p class="text-gray-500 mb-6 text-sm">Kelola informasi akun Anda di sini.</p>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nama -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all dark:bg-dark-bg dark:border-gray-700 dark:text-white" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all dark:bg-dark-bg dark:border-gray-700 dark:text-white" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Change (Optional) -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-md font-bold text-gray-800 dark:text-white mb-4">Ubah Password <span class="text-sm font-normal text-gray-400">(Opsional)</span></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password Baru</label>
                        <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all dark:bg-dark-bg dark:border-gray-700 dark:text-white">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all dark:bg-dark-bg dark:border-gray-700 dark:text-white">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-brand-blue text-white py-3 rounded-xl hover:bg-blue-600 transition-all shadow-lg shadow-brand-blue/20 font-bold">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
