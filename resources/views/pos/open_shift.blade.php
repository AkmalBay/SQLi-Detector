@extends('layouts.app')

@section('page-title', 'Buka Shift Kasir')

@section('content')
<div class="min-h-[calc(100vh-140px)] flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-dark-card rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden relative">
        
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-purple-600"></div>

        <div class="p-8">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600 dark:text-blue-400">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Buka Shift Baru</h1>
                <p class="text-gray-500 text-sm">Halo, <span class="font-bold">{{ Auth::user()->name }}</span>! Silakan input modal awal di laci kasir.</p>
            </div>

            <form action="{{ route('pos.shift.open') }}" method="POST" x-data="{ cash: '' }">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Modal Awal (Cash)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                        <input type="number" 
                               name="starting_cash" 
                               x-model="cash"
                               class="w-full pl-12 pr-4 py-4 rounded-xl bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-gray-700 focus:border-blue-500 focus:ring-0 text-xl font-bold text-gray-800 dark:text-white transition-all"
                               placeholder="0"
                               required
                               autofocus>
                    </div>
                    <div class="flex gap-2 mt-3 justify-center">
                        <button type="button" @click="cash = 100000" class="px-3 py-1 text-xs bg-gray-100 dark:bg-white/5 rounded-lg hover:bg-gray-200 transition-colors">100k</button>
                        <button type="button" @click="cash = 200000" class="px-3 py-1 text-xs bg-gray-100 dark:bg-white/5 rounded-lg hover:bg-gray-200 transition-colors">200k</button>
                        <button type="button" @click="cash = 500000" class="px-3 py-1 text-xs bg-gray-100 dark:bg-white/5 rounded-lg hover:bg-gray-200 transition-colors">500k</button>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg shadow-lg shadow-blue-500/30 transition-all transform active:scale-95">
                    Buka Kasir & Mulai Jualan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection