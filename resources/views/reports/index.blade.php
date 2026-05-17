@extends('layouts.app')

@section('page-title', 'Laporan Penjualan')

@section('content')
<div class="max-w-5xl mx-auto">
    
    <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200 dark:bg-dark-card dark:border-gray-800/50 dark:shadow-brand-blue/5">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Laporan Penjualan</h2>
                <p class="text-sm text-gray-500 mt-1">Pilih transaksi yang ingin dicetak atau gunakan filter tanggal.</p>
            </div>
            
            <div class="flex gap-4">
                <!-- Card Total -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl px-5 py-3 flex items-center gap-3 dark:bg-brand-blue/10 dark:border-brand-blue/20">
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600 dark:bg-brand-blue/20 dark:text-brand-blue">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider dark:text-brand-blue">Total Keseluruhan</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Card Tunai -->
                <div class="bg-green-50 border border-green-100 rounded-xl px-5 py-3 flex items-center gap-3 dark:bg-green-900/10 dark:border-green-800/20 hidden md:flex">
                    <div class="p-2 bg-green-100 rounded-lg text-green-600 dark:bg-green-800/30 dark:text-green-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-green-600 font-bold uppercase tracking-wider dark:text-green-400">Tunai (Cash)</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalCash, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Card Non-Tunai -->
                <div class="bg-purple-50 border border-purple-100 rounded-xl px-5 py-3 flex items-center gap-3 dark:bg-purple-900/10 dark:border-purple-800/20 hidden md:flex">
                    <div class="p-2 bg-purple-100 rounded-lg text-purple-600 dark:bg-purple-800/30 dark:text-purple-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-purple-600 font-bold uppercase tracking-wider dark:text-purple-400">Non-Tunai (TF/QRIS)</p>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalNonCash, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('reports.index') }}" method="GET" class="mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100 dark:bg-dark-bg/50 dark:border-gray-800/50">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                
                <div class="md:col-span-4">
                    <label for="start_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">Dari Tanggal</label>
                    <div class="relative">
                        <input type="date" name="start_date" id="start_date" 
                            value="{{ request('start_date') ?? (is_object($startDate) ? $startDate->format('Y-m-d') : $startDate) }}" 
                            class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all dark:bg-dark-bg dark:border-gray-700 dark:text-white [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                </div>

                <div class="md:col-span-4">
                    <label for="end_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">Sampai Tanggal</label>
                    <div class="relative">
                        <input type="date" name="end_date" id="end_date" 
                            value="{{ request('end_date') ?? (is_object($endDate) ? $endDate->format('Y-m-d') : $endDate) }}" 
                            class="w-full bg-white border border-gray-300 text-gray-800 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-all dark:bg-dark-bg dark:border-gray-700 dark:text-white [color-scheme:light] dark:[color-scheme:dark]">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-brand-blue hover:bg-blue-600 text-white font-medium py-2.5 px-4 rounded-xl shadow-lg shadow-brand-blue/20 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filter
                    </button>
                </div>
                
                <div class="md:col-span-2">
                    <a href="{{ route('reports.index') }}" class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl transition-all flex items-center justify-center gap-2 dark:bg-dark-bg dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <form action="{{ route('reports.print') }}" method="GET" target="_blank" id="printForm">
            
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">

            <div class="flex justify-between items-center mb-4 bg-blue-50 p-3 rounded-xl border border-blue-100 dark:bg-blue-900/20 dark:border-blue-800/30">
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="selectAll" class="w-5 h-5 text-brand-blue rounded border-gray-300 focus:ring-brand-blue transition cursor-pointer">
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Pilih Semua</span>
                </div>
                
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-6 rounded-lg shadow-md transition-all flex items-center gap-2 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak Terpilih</span>
                    <span id="printCount" class="bg-white text-gray-800 text-xs font-bold px-2 py-0.5 rounded-full hidden">0</span>
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800/50">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-50 text-left dark:bg-dark-bg">
                            <th class="w-10 px-6 py-4 border-b border-gray-200 dark:border-gray-800"></th>
                            <th class="px-6 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">No. Invoice</th>
                            <th class="px-6 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:border-gray-800 dark:text-gray-400">Tanggal & Waktu</th>
                            <th class="px-6 py-4 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right dark:border-gray-800 dark:text-gray-400">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800/50">
                        @forelse ($transactions as $transaction)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 group dark:hover:bg-dark-hover cursor-pointer" onclick="toggleRowCheckbox(this)">
                            
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" name="selected_ids[]" value="{{ $transaction->id }}" class="row-checkbox w-5 h-5 text-brand-blue rounded border-gray-300 focus:ring-brand-blue cursor-pointer" onclick="event.stopPropagation()" onchange="updateCount()">
                            </td>

                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-md text-xs font-mono border 
                                             bg-blue-50 text-blue-600 border-blue-100 
                                             dark:bg-gray-800 dark:text-blue-400 dark:border-gray-700">
                                    {{ $transaction->invoice_number }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $transaction->created_at->format('d M Y, H:i') }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm font-bold text-brand-green text-right">
                                Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="text-lg font-medium text-gray-400">Tidak ada data transaksi.</p>
                                    <p class="text-sm">Coba ubah filter tanggal untuk melihat data lainnya.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @if(method_exists($transactions, 'links'))
        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
        @endif

    </div>
</div>

<script>
    // Logic Select All Checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        updateCount(); // Update jumlah saat select all
    });

    // Logic Klik Baris untuk Centang Checkbox
    function toggleRowCheckbox(row) {
        const checkbox = row.querySelector('.row-checkbox');
        checkbox.checked = !checkbox.checked;
        updateCount(); // Update jumlah saat klik baris
    }

    // FUNGSI BARU: MENGHITUNG JUMLAH YANG DICENTANG
    function updateCount() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const countSpan = document.getElementById('printCount');
        const count = checkboxes.length;

        if (count > 0) {
            countSpan.innerText = count;
            countSpan.classList.remove('hidden');
        } else {
            countSpan.classList.add('hidden');
        }
    }
</script>
@endsection