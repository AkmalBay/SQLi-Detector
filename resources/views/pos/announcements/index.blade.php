@extends('layouts.app')

@section('page-title', 'Pengumuman Toko')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Papan Pengumuman</h1>
            <p class="text-gray-500">Informasi penting untuk semua staf toko.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700 font-medium flex items-center gap-2">
            &larr; Kembali
        </a>
    </div>

    <div class="space-y-4">
        @forelse($announcements as $announcement)
        <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-sm border-l-4 {{ $announcement->label == 'Penting' ? 'border-red-500' : ($announcement->label == 'Promo' ? 'border-brand-blue' : 'border-green-500') }} flex gap-4 transition-all hover:bg-gray-50 dark:hover:bg-gray-800/50">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full {{ $announcement->label == 'Penting' ? 'bg-red-100 text-red-500' : ($announcement->label == 'Promo' ? 'bg-blue-100 text-brand-blue' : 'bg-green-100 text-green-500') }} flex items-center justify-center">
                    @if($announcement->label == 'Promo')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                    @elseif($announcement->label == 'Penting')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @endif
                </div>
            </div>
            <div class="flex-1">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ $announcement->title }}</h3>
                    <span class="text-xs text-gray-400">{{ $announcement->created_at->format('d M Y, H:i') }}</span>
                </div>
                <p class="text-gray-600 dark:text-gray-300 mb-4 whitespace-pre-wrap">{{ $announcement->content }}</p>
                
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $announcement->label == 'Penting' ? 'bg-red-100 text-red-800' : ($announcement->label == 'Promo' ? 'bg-blue-100 text-brand-blue' : 'bg-green-100 text-green-800') }}">
                        {{ $announcement->label }}
                    </span>
                    <span class="text-xs text-gray-400">Diposting oleh: {{ $announcement->user->name ?? 'Admin' }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 bg-white dark:bg-dark-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="inline-flex p-4 rounded-full bg-gray-100 text-gray-400 mb-4 dark:bg-gray-800">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada pengumuman</h3>
            <p class="mt-1 text-sm text-gray-500">Informasi terbaru akan muncul di sini.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
