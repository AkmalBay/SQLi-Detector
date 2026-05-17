@extends('layouts.app')

@section('page-title', 'Manajemen Pengumuman')

@section('content')
<div class="bg-white dark:bg-dark-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Daftar Pengumuman</h2>
            <p class="text-sm text-gray-500">Kelola info penting untuk karyawan.</p>
        </div>
        <a href="{{ route('announcements.manage.create') }}" class="px-4 py-2 bg-brand-blue hover:bg-blue-600 text-white rounded-xl font-bold text-sm transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Pengumuman
        </a>
    </div>

    <div class="space-y-4">
        @forelse($announcements as $item)
        <div class="flex items-start justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
            <div class="flex gap-4">
                 <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 
                    {{ $item->label == 'Promo' ? 'bg-blue-100 text-brand-blue' : ($item->label == 'Penting' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-500') }}">
                    <span class="text-xs font-bold">{{ substr($item->label, 0, 1) }}</span>
                 </div>
                 <div>
                     <h3 class="font-bold text-gray-800 dark:text-white">{{ $item->title }}</h3>
                     <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-1">{{ $item->content }}</p>
                     <div class="flex gap-2 mt-2 text-xs">
                         <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-400">{{ $item->label }}</span>
                         <span class="{{ $item->is_active ? 'text-green-600' : 'text-gray-400' }} font-bold">
                             {{ $item->is_active ? 'Active' : 'Archived' }}
                         </span>
                         <span class="text-gray-400">• {{ $item->created_at->format('d M Y') }}</span>
                     </div>
                 </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('announcements.manage.edit', $item->id) }}" class="p-2 text-gray-400 hover:text-brand-blue">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </a>
                <form action="{{ route('announcements.manage.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-10 text-gray-500">Belum ada pengumuman.</div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $announcements->links() }}
    </div>
</div>
@endsection
