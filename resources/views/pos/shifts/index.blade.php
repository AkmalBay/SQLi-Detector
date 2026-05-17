@extends('layouts.app')

@section('page-title', 'Riwayat Laporan Shift')

@section('content')
<div class="bg-white dark:bg-dark-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
        <h2 class="font-bold text-gray-800 dark:text-white">Riwayat Setoran Saya</h2>
        <div class="text-sm text-gray-500 dark:text-gray-400">Menampilkan 10 laporan terakhir</div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
            <thead class="bg-gray-50 dark:bg-white/5 text-gray-900 dark:text-white font-semibold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="px-3 py-4">ID Shift</th>
                    <th class="px-3 py-4">Waktu Mulai</th>
                    <th class="px-3 py-4">Waktu Selesai</th>
                    <th class="px-3 py-4 text-right">Modal Awal</th>
                    <th class="px-3 py-4 text-right bg-blue-50/50 dark:bg-brand-blue/10">Setoran Fisik</th>
                    <th class="px-3 py-4 text-center bg-blue-50/50 dark:bg-brand-blue/10">Selisih Laci</th>
                    <th class="px-3 py-4 text-right bg-green-50/50 dark:bg-green-900/20 text-green-700 dark:text-green-400">Non-Tunai</th>
                    <th class="px-3 py-4 text-right font-bold text-brand-blue">Total Pendapatan</th>
                    <th class="px-3 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                @forelse($shifts as $shift)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4 font-bold text-brand-blue">#{{ $shift->id }}</td>
                    <td class="px-3 py-4 dark:text-gray-300">{{ $shift->start_time->format('d M y, H:i') }}</td>
                    <td class="px-3 py-4 dark:text-gray-300">
                        @if($shift->end_time)
                            {{ $shift->end_time->format('d M y, H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-3 py-4 text-right dark:text-gray-300">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</td>
                    <td class="px-3 py-4 text-right font-bold text-gray-800 dark:text-white bg-blue-50/20 dark:bg-brand-blue/5">
                         @if($shift->status == 'CLOSED')
                            Rp {{ number_format($shift->actual_cash, 0, ',', '.') }}
                         @else
                            -
                         @endif
                    </td>
                    <td class="px-3 py-4 text-center bg-blue-50/20 dark:bg-brand-blue/5">
                        @if($shift->status == 'CLOSED')
                            @if($shift->difference == 0)
                                <span class="px-2 py-1 rounded bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 text-[10px] font-bold">MATCH</span>
                            @elseif($shift->difference < 0)
                                <span class="px-2 py-1 rounded bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 text-[10px] font-bold">{{ number_format($shift->difference, 0, ',', '.') }}</span>
                            @else
                                <span class="px-2 py-1 rounded bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400 text-[10px] font-bold">+{{ number_format($shift->difference, 0, ',', '.') }}</span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    
                    @php
                        // Logika untuk menampilkan non-tunai dan total
                        $nonCash = 0;
                        if($shift->status == 'CLOSED') {
                            $nonCash = $shift->payments()->where('payment_method', '!=', 'CASH')->sum('amount_paid');
                        }
                        $cashRevenue = $shift->expected_cash - $shift->starting_cash;
                        $totalRevenue = $cashRevenue + $nonCash;
                    @endphp
                    
                    <td class="px-3 py-4 text-right font-semibold text-green-600 dark:text-green-400 bg-green-50/20 dark:bg-green-900/10">
                         @if($shift->status == 'CLOSED')
                            Rp {{ number_format($nonCash, 0, ',', '.') }}
                         @else
                            -
                         @endif
                    </td>
                    <td class="px-3 py-4 text-right font-bold text-brand-blue">
                         @if($shift->status == 'CLOSED')
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                         @else
                            -
                         @endif
                    </td>
                    <td class="px-3 py-4 text-center">
                        @if($shift->status == 'OPEN')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-400">
                                OPEN
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                CLOSED
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada riwayat shift.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
        {{ $shifts->links() }}
    </div>
</div>
@endsection

