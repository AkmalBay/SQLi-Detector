@extends('layouts.app')

@section('page-title', 'Tutup Shift')

@section('content')
<div class="min-h-screen p-6" x-data="moneyCounter()">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- 1. Header Section -->
        <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-gray-800/50 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-brand-blue/10 flex items-center justify-center text-brand-blue dark:bg-brand-blue/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight">Laporan Tutup Shift</h1>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                        <span>•</span>
                        <span>{{ $activeShift->start_time->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
            <div class="px-4 py-2 rounded-xl bg-green-100/80 border border-green-200 text-green-700 text-xs font-bold uppercase tracking-wide flex items-center gap-2 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Shift Open
            </div>
        </div>

        <form action="{{ route('pos.shift.close', $activeShift->id) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- 2. Main Form (The Blind Count) -->
                <div class="space-y-6">
                    <!-- Total Input Card -->
                    <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-gray-800/50 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-6 opacity-5 dark:opacity-10">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        
                        <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">Total Uang Tunai di Laci</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-2xl font-bold text-gray-400">Rp</span>
                            <input type="text" 
                                   :value="formatRupiah(totalActual)"
                                   class="w-full pl-14 pr-4 py-4 rounded-2xl bg-gray-50 dark:bg-dark-bg border-2 border-transparent focus:border-brand-blue focus:bg-white dark:focus:bg-dark-hover focus:ring-0 text-3xl font-bold text-gray-800 dark:text-white transition-all shadow-inner"
                                   readonly
                                   placeholder="0">
                            <input type="hidden" name="actual_cash" :value="totalActual">
                        </div>
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Total dihitung otomatis dari rincian uang fisik di bawah/samping.
                        </p>
                    </div>

                    <!-- Info Non-Cash Pembayaran -->
                    <div class="bg-blue-50 dark:bg-brand-blue/10 rounded-3xl p-6 border border-blue-100 dark:border-brand-blue/20 flex flex-col items-center text-center justify-center">
                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">Total Pendapatan Non-Tunai</span>
                        <span class="text-xs text-blue-500 mb-3">(Transfer, QRIS, dll)</span>
                        <div class="text-3xl font-bold text-brand-blue">Rp {{ number_format($nonCashPayments, 0, ',', '.') }}</div>
                        <p class="mt-3 text-xs text-blue-600/80 dark:text-blue-400/80">
                            Pendapatan ini otomatis tercatat di sistem dan <strong>tidak perlu dicocokkan dengan uang fisik di laci</strong>.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            onclick="return confirm('Apakah Anda yakin hitungan uang sudah benar? Shift akan berakhir dan tidak bisa diubah.')"
                            class="w-full py-4 rounded-2xl bg-brand-blue hover:bg-blue-600 text-white font-bold text-lg shadow-lg shadow-brand-blue/30 transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Finalisasi & Tutup Shift
                    </button>
                    
                    <a href="{{ route('pos.index') }}" class="block text-center text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white text-sm font-medium transition-colors">
                        Kembali ke POS (Batal)
                    </a>
                </div>

                <!-- 3. Denomination Helper -->
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-gray-800/50">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider mb-4 flex items-center justify-between">
                        <span>Rincian Uang Fisik</span>
                        <button type="button" @click="resetCounter()" class="text-xs text-red-500 hover:underline">Reset</button>
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4 max-h-[500px] overflow-y-auto custom-scrollbar pr-2">
                        <template x-for="(value, denom) in denominations" :key="denom">
                            <div class="bg-gray-50 dark:bg-dark-bg p-3 rounded-xl border border-gray-100 dark:border-gray-800 flex flex-col gap-2">
                                <label class="text-xs font-bold text-gray-500 dark:text-gray-400" x-text="'Rp ' + formatNumber(denom)"></label>
                                <div class="flex items-center gap-2">
                                    <input type="number" 
                                           x-model.number="counts[denom]" 
                                           min="0"
                                           class="w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-center font-bold text-gray-800 dark:text-white focus:border-brand-blue focus:ring-0"
                                           placeholder="0">
                                    <span class="text-xs text-gray-400">lbr</span>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Extra Coins/Lainnya -->
                        <div class="col-span-2 bg-gray-50 dark:bg-dark-bg p-3 rounded-xl border border-gray-100 dark:border-gray-800 flex flex-col gap-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400">Uang Koin / Receh Lainnya (Total Nominal)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs font-bold">Rp</span>
                                <input type="number" 
                                       x-model.number="other_amount" 
                                       min="0"
                                       class="w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-gray-700 rounded-lg pl-8 pr-3 py-2 font-bold text-gray-800 dark:text-white focus:border-brand-blue focus:ring-0"
                                       placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    function moneyCounter() {
        return {
            denominations: {
                100000: 0,
                50000: 0,
                20000: 0,
                10000: 0,
                5000: 0,
                2000: 0,
                1000: 0
            },
            counts: {
                100000: '', 50000: '', 20000: '', 10000: '', 5000: '', 2000: '', 1000: ''
            },
            other_amount: '',
            
            get totalActual() {
                let sum = 0;
                for (let denom in this.counts) {
                    let qty = parseInt(this.counts[denom]) || 0;
                    sum += qty * parseInt(denom);
                }
                sum += parseInt(this.other_amount) || 0;
                return sum;
            },
            
            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            },
            
            formatRupiah(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            },

            resetCounter() {
                for (let k in this.counts) this.counts[k] = '';
                this.other_amount = '';
            }
        }
    }
</script>
@endsection