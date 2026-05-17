@extends('layouts.app')

@section('page-title', 'Tambah Karyawan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('employees.index') }}" class="text-gray-500 hover:text-gray-700 font-medium flex items-center gap-2">
            &larr; Kembali ke Daftar Karyawan
        </a>
    </div>

    <div class="bg-white dark:bg-dark-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Informasi Karyawan Baru</h2>

        <form action="{{ route('employees.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nama -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all dark:bg-dark-bg dark:border-gray-700 dark:text-white" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all dark:bg-dark-bg dark:border-gray-700 dark:text-white" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role / Jabatan</label>
                <select name="role" id="role" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all dark:bg-dark-bg dark:border-gray-700 dark:text-white" required>
                    <option value="" disabled selected>Pilih Jabatan</option>
                    @foreach($roles as $role)
                        @if($role->name !== 'pemilik')
                            <option value="{{ $role->name }}">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                    <input type="password" name="password" id="password" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all dark:bg-dark-bg dark:border-gray-700 dark:text-white" required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-brand-blue focus:ring-2 focus:ring-brand-blue/20 outline-none transition-all dark:bg-dark-bg dark:border-gray-700 dark:text-white" required>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-brand-blue text-white py-3 rounded-xl hover:bg-blue-600 transition-all shadow-lg shadow-brand-blue/20 font-bold">
                    Simpan Karyawan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
