@extends('layouts.app')

@section('page-title', isset($announcement) ? 'Edit Pengumuman' : 'Buat Pengumuman')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-dark-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-8">
    
    <form action="{{ isset($announcement) ? route('announcements.manage.update', $announcement->id) : route('announcements.manage.store') }}" method="POST" class="space-y-6">
        @csrf
        @if(isset($announcement)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Judul Pengumuman</label>
            <input type="text" name="title" value="{{ old('title', $announcement->title ?? '') }}" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-gray-700 focus:border-brand-blue focus:ring-0" placeholder="Contoh: Promo Diskon 50%" required>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Isi Konten</label>
            <textarea name="content" rows="4" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-gray-700 focus:border-brand-blue focus:ring-0" placeholder="Tulis detail pengumuman disini..." required>{{ old('content', $announcement->content ?? '') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Label / Kategori</label>
                <select name="label" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-gray-700 focus:border-brand-blue focus:ring-0">
                    <option value="Info" {{ (old('label', $announcement->label ?? '') == 'Info') ? 'selected' : '' }}>Info</option>
                    <option value="Promo" {{ (old('label', $announcement->label ?? '') == 'Promo') ? 'selected' : '' }}>Promo</option>
                    <option value="Penting" {{ (old('label', $announcement->label ?? '') == 'Penting') ? 'selected' : '' }}>Penting</option>
                </select>
            </div>
            
            <div class="flex items-center pt-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-brand-blue rounded focus:ring-brand-blue/50" {{ (old('is_active', $announcement->is_active ?? true)) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Publikasikan (Active)</span>
                </label>
            </div>
        </div>

        <div class="pt-6 flex gap-4">
            <a href="{{ route('announcements.manage.index') }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors">Batal</a>
            <button type="submit" class="flex-1 px-6 py-3 rounded-xl bg-brand-blue text-white font-bold hover:bg-blue-600 transition-colors shadow-lg shadow-brand-blue/20">
                {{ isset($announcement) ? 'Simpan Perubahan' : 'Terbitkan Pengumuman' }}
            </button>
        </div>
    </form>
</div>
@endsection
