@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
{{-- Container Utama: Tidak perlu grid pembungkus lagi, langsung stack ke bawah --}}
<div class="space-y-6">

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h2 class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-white">Selamat Datang, {{ Auth::user()->name }} 👋</h2>
            <p class="text-gray-500 text-sm">Berikut adalah ringkasan performa toko hari ini.</p>
        </div>

        {{-- Status Shift Khusus Kasir --}}
        @if(auth()->user()->hasRole('kasir'))
            <div class="w-fit bg-green-100 text-green-700 px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Shift Open
            </div>
        @endif
    </div>

    {{-- PENGUMUMAN UNTUK SEMUA USER --}}
    @php $latestAnnouncement = \App\Models\Announcement::latest()->first(); @endphp
    @if($latestAnnouncement)
    <div class="bg-gradient-to-br from-purple-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="flex justify-between items-start relative z-10">
            <div>
                <span class="inline-block px-3 py-1 bg-white/20 rounded-lg text-xs font-bold mb-3 backdrop-blur-sm">{{ $latestAnnouncement->label ?? 'Info Toko' }}</span>
                <h3 class="text-xl font-bold mb-2">{{ $latestAnnouncement->title }}</h3>
                <p class="text-purple-100 text-sm mb-4 max-w-lg line-clamp-2">{{ $latestAnnouncement->content }}</p>
                <a href="{{ route('announcements.index') }}" class="text-sm font-semibold underline hover:text-purple-200">Lihat Semua Pengumuman &rarr;</a>
            </div>
        </div>
    </div>
    @endif

    {{-- KONTEN UNTUK KASIR --}}
    @if(auth()->user()->hasRole('kasir'))
    <div class="space-y-6">
        
        {{-- Row 1: Ringkasan Shift & Transaksi --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card Sales Saya --}}
            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 rounded-xl bg-green-100 text-green-600 dark:bg-green-900/20 dark:text-green-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-xs font-medium dark:text-gray-400">Sales Saya</h3>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">Rp {{ number_format($stats['my_sales_today'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Card Transaksi Saya --}}
            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-xs font-medium dark:text-gray-400">Transaksi</h3>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $myTransactionCount ?? 0 }}</p>
                    </div>
                </div>
            </div>

             {{-- Card Shift Time --}}
             <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-xs font-medium dark:text-gray-400">Shift Mulai</h3>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">{{ now()->format('H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 2: TOMBOL POS BESAR --}}
        <div class="bg-gradient-to-r from-brand-blue to-blue-600 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Point of Sales System</h3>
                    <p class="text-blue-100 opacity-90">Siap melayani pelanggan? Buka aplikasi kasir sekarang.</p>
                </div>
                <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-3 bg-white text-brand-blue px-8 py-4 rounded-xl font-bold hover:bg-blue-50 transition shadow-lg transform hover:-translate-y-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Buka Kasir / POS
                </a>
            </div>
        </div>

        {{-- Row 3: Grafik Kasir --}}
        <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-800">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Produk Terlaris (Bulan Ini)</h3>
            <div class="h-48 md:h-64">
                <canvas id="topSellingChart"></canvas>
            </div>
        </div>
    </div>

    {{-- KONTEN UNTUK ADMIN GUDANG --}}
    @elseif(auth()->user()->hasAnyRole(['admin_gudang', 'pekerja_gudang']))
    <div class="space-y-6">
        
        {{-- Row 1: INVENTORY STATS CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Card 1: Barang Masuk (Hari Ini) --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-green-100 rounded-xl text-green-600 dark:bg-green-900/20 dark:text-green-400">
                        {{-- Icon Panah Masuk --}}
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 mb-1">Barang Masuk (Hari Ini)</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($barangMasuk ?? 0) }}</h3>
            </div>

            {{-- Card 2: Barang Keluar (Hari Ini) --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-orange-100 rounded-xl text-orange-600 dark:bg-orange-900/20 dark:text-orange-400">
                        {{-- Icon Panah Keluar --}}
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 mb-1">Barang Keluar (Hari Ini)</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($barangKeluar ?? 0) }}</h3>
            </div>

            {{-- Card 3: Total SKU --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-blue-100 rounded-xl text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                        {{-- Icon Box --}}
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Produk (SKU)</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($stats['total_products'] ?? 0) }}</h3>
            </div>

            {{-- Card 4: Low Stock Alert --}}
            <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="block group">
                 <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow cursor-pointer border-l-4 border-l-yellow-400">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-yellow-100 rounded-xl text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400 group-hover:bg-yellow-200 transition-colors">
                            {{-- Icon Alert --}}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Perlu Restock</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($lowStock ?? 0) }} <span class="text-sm font-normal text-gray-400">Item</span></h3>
                </div>
            </a>
        </div>

        {{-- Row 2: Charts & Mutasi --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Bagian Kiri: Tabel Mutasi Stok (Lebar 2 Kolom) --}}
            <div class="lg:col-span-2 bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Mutasi Stok Terakhir</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($recentMutations ?? [] as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log->created_at->format('d M H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $log->product->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->product->sku ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->type == 'in')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Masuk
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold">
                                    {{ $log->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log->user->name ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada mutasi stok hari ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bagian Kanan: Grafik Low Stock --}}
             <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-4 dark:text-white">Top 5 Stok Menipis</h3>
                <div class="h-48 md:h-64">
                    <canvas id="lowStockChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    {{-- KONTEN UNTUK PEMILIK (Fallback Existing tapi dipercantik UI-nya) --}}
    @else 
    <div class="space-y-6">
        
        {{-- Row 1: 4 Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            {{-- Card 1: Total Penjualan --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-brand-blue/10 rounded-xl text-brand-blue">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    {{-- Badge Growth --}}
                    @if(($stats['sales_growth'] ?? 0) >= 0)
                        <span class="flex items-center text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded-lg">+{{ $stats['sales_growth'] ?? 0 }}%</span>
                    @else
                        <span class="flex items-center text-xs font-bold text-red-600 bg-red-100 px-2 py-1 rounded-lg">{{ $stats['sales_growth'] ?? 0 }}%</span>
                    @endif
                </div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Penjualan</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Rp {{ number_format($stats['sales_this_month'] ?? 0, 0, ',', '.') }}</h3>
            </div>

            {{-- Card 2: Total Transaksi --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-brand-purple/10 rounded-xl text-brand-purple">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Transaksi</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($stats['total_transactions'] ?? 0) }}</h3>
            </div>

            {{-- Card 3: Produk Baru --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-brand-orange/10 rounded-xl text-brand-orange">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 mb-1">Produk Baru</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['new_products_this_month'] ?? 0 }}</h3>
            </div>

            {{-- Card 4: Total Produk --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-brand-green/10 rounded-xl text-brand-green">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Produk</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($stats['total_products'] ?? 0) }}</h3>
            </div>
        </div>

        {{-- Row 2: Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Chart: Kategori --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-4 dark:text-white">Komposisi Kategori</h3>
                <div class="h-48 md:h-64 flex justify-center items-center">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            {{-- Chart: Low Stock (Sekarang Muncul untuk Pemilik) --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-4 dark:text-white">Top 5 Stok Menipis</h3>
                <div class="h-64 relative">
                    <canvas id="lowStockChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Row 4: Financial Charts (Hanya Pemilik) --}}
        @if(auth()->user()->hasRole('pemilik'))
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Chart: Penjualan --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-4 dark:text-white">Statistik Penjualan</h3>
                <div class="h-64">
                     <canvas id="salesChart"></canvas>
                </div>
            </div>

            {{-- Chart: Status Stok --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 dark:bg-dark-card dark:border-gray-800 hover:shadow-md transition-shadow">
                <h3 class="text-lg font-bold text-gray-800 mb-4 dark:text-white">Status Stok Produk</h3>
                <div class="h-64 flex justify-center">
                    <canvas id="stockChart"></canvas>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>

{{-- SECTION SCRIPTS UNTUK CHART.JS --}}
{{-- Kasir Script --}}
@if(auth()->user()->hasRole('kasir') && isset($topSellingData))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('topSellingChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($topSellingData['labels']),
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: @json($topSellingData['values']),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Horizontal Bar Chart
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    y: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endif

{{-- Admin/Owner Scripts --}}
@if(auth()->user()->hasAnyRole(['admin_gudang', 'pemilik', 'pekerja_gudang']))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart 1: Category Distribution
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            new Chart(categoryCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryData['labels'] ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($categoryData['values'] ?? []) !!},
                        backgroundColor: ['#4c6fff', '#9f85ff', '#ff8a65', '#4db6ac'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    cutout: '60%'
                }
            });
        }

        // Chart 2: Low Stock
        const lowStockCtx = document.getElementById('lowStockChart');
        if (lowStockCtx) {
            new Chart(lowStockCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($lowStockData['labels'] ?? []) !!},
                    datasets: [{
                        label: 'Stok Tersisa',
                        data: {!! json_encode($lowStockData['values'] ?? []) !!},
                        backgroundColor: '#ef4444',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: { legend: { display: false } },
                    layout: {
                        padding: {
                            left: 20,
                            right: 30 
                        }
                    }
                }
            });
        }
    });
</script>
@endif

{{-- Owner Specific Scripts --}}
@if(auth()->user()->hasRole('pemilik') && isset($salesChartData))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($salesChartData['labels']),
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: @json($salesChartData['values']),
                    borderColor: '#4c6fff',
                    backgroundColor: 'rgba(76, 111, 255, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });

        // Stock Chart
        const stockCtx = document.getElementById('stockChart').getContext('2d');
        new Chart(stockCtx, {
            type: 'doughnut',
            data: {
                labels: ['Tersedia', 'Menipis', 'Habis'],
                datasets: [{
                    data: @json($stockChartData ?? [0,0,0]),
                    backgroundColor: ['#4db6ac', '#ff8a65', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    });
</script>
@endif

@endsection