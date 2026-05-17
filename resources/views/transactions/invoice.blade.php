<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        dark: { bg: '#0c0e12', card: '#151921', border: '#2d3748' },
                        brand: { blue: '#4c6fff', green: '#4db6ac' }
                    }
                }
            }
        }
    </script>

    <style>
        body { transition: background-color 0.3s ease, color 0.3s ease; }
        :root, html.light body { background-color: #f3f4f6; color: #374151; }
        html.dark body { background-color: #0c0e12; color: #a0aec0; }

        @media print {
            @page { margin: 0; size: auto; }
            body {
                background-color: white !important;
                color: black !important;
                -webkit-print-color-adjust: exact;
                margin: 0; padding: 0;
            }
            .no-print { display: none !important; }
            .invoice-card {
                background-color: white !important;
                box-shadow: none !important;
                border: none !important;
                color: black !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 20px !important;
            }
            .bg-dark-bg { background-color: #f3f4f6 !important; color: black !important; }
            .text-white, .dark\:text-white { color: black !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
    
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-6 print:p-0 print:block">

    <div class="w-full max-w-3xl mx-auto">
        
        <div class="flex justify-between items-center mb-6 no-print">
            <a href="{{ route('dashboard') }}" class="flex items-center text-gray-500 hover:text-brand-blue transition-colors dark:text-gray-400 dark:hover:text-white">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </a>
            <div class="flex gap-2">
                <button onclick="window.print()" class="bg-brand-blue text-white px-5 py-2 rounded-xl shadow-lg hover:bg-blue-600 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print (A4)
                </button>
            </div>
        </div>

        <div class="invoice-card bg-white rounded-2xl p-8 shadow-xl border border-gray-100 relative overflow-hidden dark:bg-dark-card dark:border-gray-800/50 dark:shadow-black/50 transition-colors duration-300">
            
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-brand-blue to-purple-500 no-print"></div>

            <div class="flex flex-col sm:flex-row justify-between items-start mb-8 border-b border-gray-100 pb-8 gap-6 dark:border-gray-800">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg bg-brand-blue flex items-center justify-center text-white font-bold text-xl shadow-lg print:bg-black print:text-white">S</div>
                        <h1 class="text-2xl font-bold text-gray-800 tracking-wide uppercase dark:text-white">Toko Sembako Brayan</h1>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed max-w-xs dark:text-gray-400">
                        Jl. Cendrawasih RT 08/08, Bajing Kulon,<br>
                        Kroya, Cilacap, Jawa Tengah
                    </p>
                    <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">Telp: 08xx-xxxx-xxxx</p> 
                </div>

                <div class="text-left sm:text-right">
                    <h2 class="text-4xl font-bold text-gray-100 mb-2 uppercase dark:text-white/10 print:text-gray-200">Invoice</h2>
                    <p class="text-gray-800 font-mono text-lg font-semibold dark:text-white">#{{ $transaction->invoice_number }}</p>
                    <p class="text-sm text-gray-500 mt-1 dark:text-gray-400">Tanggal: {{ $transaction->created_at->format('d F Y, H:i') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kasir: {{ $transaction->user->name ?? 'Admin' }}</p>
                </div>
            </div>

            <div class="overflow-x-auto mb-8">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-y border-gray-100 dark:bg-dark-bg dark:border-gray-800 print:border-gray-300 print:bg-gray-100">
                            <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Produk</th>
                            <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center dark:text-gray-400">Qty</th>
                            <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right dark:text-gray-400">Harga Satuan</th>
                            <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right dark:text-gray-400">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 print:divide-gray-300">
                        @foreach ($transaction->items as $item)
                        <tr>
                            <td class="py-4 px-4 font-medium text-gray-800 dark:text-white">
                                {{ $item->product->name }}
                            </td>
                            <td class="py-4 px-4 text-gray-600 text-center dark:text-gray-300">{{ $item->quantity }}</td>
                            <td class="py-4 px-4 text-gray-600 text-right dark:text-gray-300">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="py-4 px-4 font-bold text-gray-800 text-right dark:text-white">
                                Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col sm:flex-row justify-end items-center sm:items-start border-t border-gray-100 pt-6 dark:border-gray-800 print:border-gray-300">
                <div class="w-full sm:w-1/2 space-y-3">
                    
                    <div class="flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                        <span>Total Tagihan</span>
                        <span class="font-bold text-lg text-gray-800 dark:text-white">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="border-t border-dashed border-gray-200 dark:border-gray-700 my-2"></div>

                    @foreach($transaction->payments as $payment)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600 dark:text-gray-300">
                            Bayar ({{ $payment->payment_method }})
                            @if($payment->reference_number)
                                <span class="text-xs text-gray-400">#{{ $payment->reference_number }}</span>
                            @endif
                        </span>
                        <span class="font-medium text-green-600 dark:text-green-400">Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}</span>
                    </div>
                    @endforeach

                    <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-800 print:border-gray-300">
                        <span class="text-lg font-bold text-gray-800 uppercase tracking-wide dark:text-white">Kembali</span>
                        <span class="text-2xl font-bold text-brand-blue print:text-black">
                            Rp {{ number_format($transaction->change, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center pt-8 border-t border-gray-100 dark:border-gray-800/50 print:border-gray-300 print:mt-8">
                <p class="font-medium text-sm text-gray-800 dark:text-white">Terima Kasih Telah Berbelanja!</p>
                <p class="text-xs text-gray-500 mt-1 dark:text-gray-600">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
                
                <div class="mt-4 no-print text-xs text-gray-400">
                    System by Laravel POS
                </div>
            </div>
        </div>

    </div>

</body>
</html>