@extends('layouts.app')

@section('page-title', 'Manajemen Supplier')

@section('content')
<div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200 dark:bg-dark-card dark:border-gray-800/50 dark:shadow-brand-blue/5">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Daftar Supplier</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data pemasok dan informasi kontak mereka.</p>
        </div>
        
        @if(auth()->user()->hasAnyRole(['admin_gudang', 'pemilik']))
        <a href="{{ route('suppliers.create') }}" class="bg-brand-blue text-white px-5 py-2.5 rounded-xl hover:bg-blue-600 transition-all duration-200 shadow-lg shadow-brand-blue/20 flex items-center gap-2 group">
            <svg class="w-5 h-5 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            <span>Tambah Supplier</span>
        </a>
        @endif
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800/50">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-50 text-left dark:bg-dark-bg">
                    <th class="px-6 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Nama Supplier</th>
                    <th class="px-6 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Kontak Person</th>
                    <th class="px-6 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Telepon</th>
                    <th class="px-6 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Alamat</th>
                    <th class="px-6 py-4 border-b border-gray-200 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800/50">
                @forelse ($suppliers as $supplier)
                <tr class="hover:bg-gray-50 transition-colors duration-150 group dark:hover:bg-dark-hover">
                    
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold mr-3 border 
                                        bg-purple-100 text-purple-600 border-purple-200 
                                        dark:bg-purple-500/20 dark:text-purple-400 dark:border-purple-500/30">
                                {{ substr($supplier->name, 0, 1) }}
                            </div>
                            <span class="font-medium text-gray-800 dark:text-white">{{ $supplier->name }}</span>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ $supplier->contact_person }}
                        </div>
                    </td>

                    <td class="px-6 py-4 text-sm">
                        <a href="tel:{{ $supplier->phone }}" class="text-brand-blue hover:underline flex items-center gap-1 font-medium">
                            {{ $supplier->phone }}
                        </a>
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate dark:text-gray-400" title="{{ $supplier->address }}">
                        {{ $supplier->address }}
                    </td>

                    <td class="px-6 py-4 text-sm text-right">
                        <div class="flex items-center justify-end gap-3 opacity-70 group-hover:opacity-100 transition-opacity">
                            @if(auth()->user()->hasAnyRole(['admin_gudang', 'pemilik']))
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="p-2 rounded-lg transition-all 
                                      bg-blue-50 text-blue-600 hover:bg-blue-100 
                                      dark:bg-blue-500/10 dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white" 
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>

                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg transition-all 
                                               bg-red-50 text-red-600 hover:bg-red-100 
                                               dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500 dark:hover:text-white" 
                                        title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <p>Belum ada supplier yang terdaftar.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection