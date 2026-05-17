@extends('layouts.app')

@section('page-title', 'Laporan Shift')

@section('content')
<div class="min-h-screen p-6 flex flex-col items-center justify-center">
    
    <div class="w-full max-w-4xl bg-white dark:bg-dark-card rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800/50 overflow-hidden">
        <!-- 1. Result Header -->
        <div class="bg-gray-50 dark:bg-white/5 p-8 text-center border-b border-gray-100 dark:border-gray-800/50">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full {{ $shift->difference === 0 ? 'bg-green-100 text-green-600' : ($shift->difference < 0 ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600') }} mb-4 shadow-lg">
                @if($shift->difference === 0)
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                @elseif($shift->difference < 0)
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                @else
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                @endif
            </div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Shift #{{ $shift->id }} Berakhir</h1>
            <p class="text-gray-500 dark:text-gray-400">
                Pencocokan selesai. 
                <span class="font-bold {{ $shift->difference === 0 ? 'text-green-600' : ($shift->difference < 0 ? 'text-red-500' : 'text-orange-500') }}">
                    {{ $shift->difference === 0 ? 'Saldo Seimbang (Balance)' : ($shift->difference < 0 ? 'Saldo Kurang (Short)' : 'Saldo Lebih (Surplus)') }}
                </span>
            </p>
        </div>

        <!-- 2. Ringkasan Total Pendapatan -->
        <div class="px-8 pt-8 pb-4">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 text-center">Ringkasan Total Pendapatan Shift</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-dark-bg text-center">
                    <div class="text-xs text-gray-500 mb-1">Pendapatan Tunai</div>
                    <div class="text-lg font-bold text-gray-700 dark:text-gray-200">Rp {{ number_format($shift->expected_cash - $shift->starting_cash, 0, ',', '.') }}</div>
                </div>
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-dark-bg text-center">
                    <div class="text-xs text-gray-500 mb-1">Pendapatan Non-Tunai</div>
                    <div class="text-lg font-bold text-gray-700 dark:text-gray-200">Rp {{ number_format($nonCashPayments, 0, ',', '.') }}</div>
                    <div class="text-[10px] text-gray-400 mt-1">(Transfer, QRIS, dll)</div>
                </div>
                <div class="p-4 rounded-xl bg-blue-50 dark:bg-brand-blue/10 border border-blue-100 dark:border-brand-blue/20 text-center">
                    <div class="text-xs font-bold text-brand-blue mb-1">Total KESELURUHAN</div>
                    <div class="text-2xl font-bold text-brand-blue">Rp {{ number_format(($shift->expected_cash - $shift->starting_cash) + $nonCashPayments, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- 3. Summary Section (Comparison Grid) -->
        <div class="p-8">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 text-center">Pencocokan Uang Fisik di Laci</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <!-- Col 1: System -->
                <div class="p-6 rounded-2xl bg-gray-50 border border-gray-100 dark:bg-dark-bg dark:border-gray-800 text-center">
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Sistem Komputer</div>
                    <div class="text-xl font-bold text-gray-600 dark:text-gray-300">Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}</div>
                    <div class="text-xs text-gray-400 mt-1">Expected</div>
                </div>

                <!-- Col 2: Actual -->
                <div class="p-6 rounded-2xl bg-brand-blue shadow-lg shadow-brand-blue/30 text-center transform scale-105 z-10">
                    <div class="text-xs font-bold text-blue-200 uppercase tracking-wider mb-2">Uang Fisik</div>
                    <div class="text-2xl font-bold text-white">Rp {{ number_format($shift->actual_cash, 0, ',', '.') }}</div>
                    <div class="text-xs text-blue-100 mt-1">Actual Count</div>
                </div>

                <!-- Col 3: Variance -->
                <div class="p-6 rounded-2xl border text-center {{ $shift->difference < 0 ? 'bg-red-50 border-red-100 dark:bg-red-900/10 dark:border-red-900/30' : ($shift->difference > 0 ? 'bg-orange-50 border-orange-100 dark:bg-orange-900/10 dark:border-orange-900/30' : 'bg-green-50 border-green-100 dark:bg-green-900/10 dark:border-green-900/30') }}">
                    <div class="text-xs font-bold uppercase tracking-wider mb-2 {{ $shift->difference < 0 ? 'text-red-500' : ($shift->difference > 0 ? 'text-orange-500' : 'text-green-500') }}">Selisih (Variance)</div>
                    <div class="text-xl font-bold {{ $shift->difference < 0 ? 'text-red-600' : ($shift->difference > 0 ? 'text-orange-600' : 'text-green-600') }}">
                        {{ $shift->difference < 0 ? '-' : '+' }} Rp {{ number_format(abs($shift->difference), 0, ',', '.') }}
                    </div>
                     <div class="text-xs mt-1 {{ $shift->difference < 0 ? 'text-red-400' : ($shift->difference > 0 ? 'text-orange-400' : 'text-green-400') }}">
                        {{ $shift->difference === 0 ? 'Perfect Match' : 'Attention Needed' }}
                    </div>
                </div>
            </div>

            <!-- 4. Footer Actions -->
            <div class="flex flex-col md:flex-row items-center justify-center gap-4">
                <button onclick="window.print()" class="w-full md:w-auto px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold transition-colors dark:bg-white/10 dark:hover:bg-white/20 dark:text-white flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Laporan
                </button>
                <a href="{{ route('dashboard') }}" class="w-full md:w-auto px-8 py-3 rounded-xl bg-gray-800 hover:bg-gray-900 text-white font-bold transition-colors dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200 flex items-center justify-center gap-2">
                    Kembali ke Dashboard
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
