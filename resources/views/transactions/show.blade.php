@extends('layouts.app')

@section('page-title', 'Detail Transaksi')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="mb-6">
        @if(request('from') == 'transaction')
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center text-gray-500 hover:text-brand-blue transition-colors dark:text-gray-400 dark:hover:text-white">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Menu Kasir
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-brand-blue transition-colors dark:text-gray-400 dark:hover:text-white">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </a>
        @endif
    </div>

    <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-lg relative overflow-hidden dark:bg-dark-card dark:border-gray-800/50">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200 pb-6 mb-6 dark:border-gray-800">
            <div>
                <span class="bg-blue-100 text-brand-blue border border-blue-200 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide dark:bg-brand-blue/10 dark:border-brand-blue/20">
                    Completed
                </span>
                <h2 class="text-3xl font-bold text-gray-800 mt-3 dark:text-white">Invoice #{{ $transaction->invoice_number }}</h2>
                <p class="text-gray-500 text-sm mt-1">Dibuat pada {{ $transaction->created_at->format('d F Y, H:i') }}</p>
            </div>
            <div class="mt-4 md:mt-0 text-right">
                <a href="{{ route('transactions.invoice', $transaction->id) }}" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 border border-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white dark:border-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Invoice
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200 mb-8 dark:border-gray-800/50">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase dark:bg-dark-bg dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Produk</th>
                        <th class="px-6 py-4 font-semibold text-center">Harga Satuan</th>
                        <th class="px-6 py-4 font-semibold text-center">Qty</th>
                        <th class="px-6 py-4 font-semibold text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-600 dark:divide-gray-800/50 dark:text-gray-300">
                    @foreach ($transaction->items as $item)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">{{ $item->product->name }}</td>
                        <td class="px-6 py-4 text-center">Rp {{ number_format($item->product->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-right font-bold text-gray-800 dark:text-white">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <div class="w-full md:w-1/3 space-y-3">
                <div class="flex justify-between text-gray-500 text-sm dark:text-gray-400">
                    <span>Subtotal</span>
                    <span class="text-gray-800 font-medium dark:text-gray-300">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xl font-bold pt-4 border-t border-gray-200 dark:border-gray-800">
                    <span class="text-gray-800 dark:text-white">Total Pembayaran</span>
                    <span class="text-brand-green">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
