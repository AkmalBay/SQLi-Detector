@extends('layouts.app')

@section('page-title', 'Riwayat Transaksi')

@section('content')
<div class="bg-white dark:bg-dark-card rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
    <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-bold text-gray-800 dark:text-white">Riwayat Transaksi</h2>
            <p class="text-xs text-gray-500">
                @if(auth()->user()->hasRole('kasir'))
                    Daftar transaksi Anda
                @else
                    Semua transaksi toko
                @endif
            </p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-white/5 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-4">Waktu</th>
                    <th class="px-6 py-4">Invoice</th>
                    <th class="px-6 py-4 text-center">Item</th>
                    <th class="px-6 py-4 text-right">Total</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm dark:divide-white/5">
                @forelse ($transactions as $trx)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                        {{ $trx->created_at->format('d M Y H:i') }}
                        <br>
                        <span class="text-xs text-gray-400">{{ $trx->created_at->diffForHumans() }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-mono font-medium text-brand-blue">{{ $trx->invoice_number }}</span>
                    </td>
                    <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-300">
                        {{ $trx->items->sum('quantity') }} Item
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-gray-800 dark:text-white">
                        Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center flex justify-center gap-2">
                        {{-- REPRINT --}}
                        <a href="#" onclick="window.open('{{ route('transactions.invoice', $trx->id) }}', '_blank'); return false;"
                           class="bg-blue-50 text-brand-blue hover:bg-brand-blue hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print
                        </a>

                        {{-- VOID (Hanya Untuk Pemilik) --}}
                        @role('pemilik')
                        <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini? Stok akan dikembalikan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Void
                            </button>
                        </form>
                        @endrole
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Belum ada riwayat transaksi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-6 border-t border-gray-100 dark:border-gray-800">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
