<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Meta Tags untuk SEO -->
    <meta name="description" content="Sistem POS dan Manajemen Stok untuk Toko Sembako Brayan. Kelola stok, transaksi, dan laporan dengan mudah.">
    <meta name="keywords" content="POS, point of sale, stok barang, inventory, sembako, kasir, toko, minimarket, aplikasi kasir">
    <meta name="author" content="Toko Sembako Brayan">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og-title', 'Aplikasi POS - Toko Sembako Brayan')">
    <meta property="og:description" content="Sistem POS dan Manajemen Stok untuk Toko Sembako Brayan">
    <meta property="og:image" content="{{ asset('apple-touch-icon.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary">
    <meta property="twitter:title" content="@yield('twitter-title', 'Aplikasi POS - Toko Sembako Brayan')">
    <meta property="twitter:description" content="Sistem POS dan Manajemen Stok untuk Toko Sembako Brayan">
    <meta property="twitter:image" content="{{ asset('apple-touch-icon.png') }}">

    <!-- Favicon & Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @if(file_exists(public_path('favicon-32x32.png')))
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    @endif
    @if(file_exists(public_path('favicon-16x16.png')))
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    @endif
    @if(file_exists(public_path('apple-touch-icon.png')))
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    @endif
    @if(file_exists(public_path('site.webmanifest')))
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    @endif

    <!-- Theme Color -->
    <meta name="theme-color" content="#4c6fff">

    <!-- Title -->
    <title>@yield('page-title', 'Aplikasi POS') - Toko Sembako Brayan</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class', 
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        dark: {
                            bg: '#0c0e12',      
                            card: '#151921',    
                            hover: '#1e2330',
                            border: '#2d3748'   
                        },
                        brand: {
                            blue: '#4c6fff',    
                            purple: '#9f85ff',  
                            orange: '#ff8a65',  
                            green: '#4db6ac',   
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: #2d3748; }
        
        [x-cloak] { display: none !important; }
        
        /* Transisi warna halus */
        body, aside, header, div, input, select, button { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }

        /* --- STYLE KURSOR GELEMBUNG --- */
        @media (max-width: 1024px) {
            .cursor-dot, .cursor-bubble { display: none !important; }
            body { cursor: auto !important; }
        }
        @media (min-width: 1025px) {
            body { cursor: none; } 
        }
        .cursor-dot {
            position: fixed; top: 0; left: 0; width: 8px; height: 8px;
            background-color: #4c6fff; border-radius: 50%; z-index: 9999;
            pointer-events: none; transform: translate(-50%, -50%);
            transition: transform 0.1s, background-color 0.2s;
        }
        .cursor-bubble {
            position: fixed; top: 0; left: 0; width: 30px; height: 30px;
            border-radius: 50%; z-index: 9998;
            pointer-events: none; transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s, border-color 0.3s, background-color 0.3s, opacity 0.3s;
            will-change: transform;
            border: 1px solid rgba(76, 111, 255, 0.3);
            background-color: rgba(76, 111, 255, 0.02);
        }
        html.light .cursor-bubble {
            border-color: rgba(76, 111, 255, 0.6); 
            background-color: rgba(76, 111, 255, 0.05);
        }
        body.hovering .cursor-dot {
            transform: translate(-50%, -50%) scale(1.8); background-color: #60a5fa;
        }
        body.hovering .cursor-bubble {
            border-color: rgba(76, 111, 255, 0.8); background-color: rgba(76, 111, 255, 0.15);
        }
    </style>
</head>

<body class="antialiased flex h-screen text-sm bg-gray-100 text-gray-800 dark:bg-[#0c0e12] dark:text-[#a0aec0] overflow-hidden"
    x-data="{ 
        sidebarOpen: false,
        mobileSidebarOpen: false,
        mouseX: 0, 
        mouseY: 0,
        isHovering: false,
        darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        }
    }"
    x-init="$watch('darkMode', val => {
        if(val) { document.documentElement.classList.add('dark'); document.documentElement.classList.remove('light'); }
        else { document.documentElement.classList.remove('dark'); document.documentElement.classList.add('light'); }
    }); 
    if(darkMode) { document.documentElement.classList.add('dark'); document.documentElement.classList.remove('light'); }
    else { document.documentElement.classList.remove('dark'); document.documentElement.classList.add('light'); }"
    @mousemove="mouseX = $event.clientX; mouseY = $event.clientY"
    @mouseover="isHovering = ($event.target.closest('a, button, input, select, .cursor-pointer') !== null)"
>

    <!-- Overlay for Mobile Sidebar -->
    <div 
        x-show="mobileSidebarOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        @click="mobileSidebarOpen = false"
    ></div>

    <aside 
        @mouseenter="if(window.innerWidth > 1024) sidebarOpen = true"
        @mouseleave="if(window.innerWidth > 1024) sidebarOpen = false"
        class="bg-white flex flex-col h-full border-r border-gray-200 fixed lg:relative z-50 transition-all duration-300 ease-in-out dark:bg-dark-card dark:border-gray-800/50 shadow-2xl lg:shadow-none"
        :class="[
            mobileSidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0',
            sidebarOpen ? 'lg:w-64' : 'lg:w-20'
        ]"
    >
        <div class="h-20 flex-shrink-0 flex items-center justify-between px-4 border-b border-gray-200 overflow-hidden dark:border-gray-800/30">
            <div class="flex items-center gap-3 w-full" :class="(sidebarOpen || mobileSidebarOpen) ? 'justify-start px-2' : 'justify-center'">
                <div class="w-8 h-8 min-w-[2rem] rounded-lg bg-brand-blue flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-brand-blue/20">S</div>
                <span x-show="sidebarOpen || mobileSidebarOpen" x-transition.opacity.duration.300ms class="text-gray-800 font-semibold text-lg tracking-wide whitespace-nowrap dark:text-white">
                    Sembako
                </span>
            </div>
            <button @click="mobileSidebarOpen = false" class="lg:hidden p-2 text-gray-500 hover:text-brand-blue">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <nav class="flex-1 px-3 space-y-2 mt-4 overflow-y-auto overflow-x-hidden custom-scrollbar">
            @php
                // DEFINISI MENU BERDASARKAN ROLE (Updated)
                $menuItems = [
                    // 1. Dashboard (Top Level)
                    [
                        'type' => 'link',
                        'route' => 'dashboard',
                        'label' => 'Dashboard',
                        'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
                        'roles' => null // All
                    ],
                    
                    // 2. Group: GUDANG (Admin Gudang, Pemilik, Pekerja)
                    [
                        'type' => 'group',
                        'label' => 'Gudang',
                        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', // Box icon
                        'id' => 'gudang',
                        'roles' => ['admin_gudang', 'pemilik', 'pekerja_gudang'],
                        'items' => [
                            [
                                'route' => 'products.index',
                                'label' => 'Produk',
                                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                                'roles' => ['admin_gudang', 'pemilik', 'pekerja_gudang']
                            ],
                            [
                                'route' => 'suppliers.index',
                                'label' => 'Supplier',
                                'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
                                'roles' => ['admin_gudang', 'pemilik', 'pekerja_gudang']
                            ],
                            [
                                'route' => 'inventory.index',
                                'label' => 'Riwayat Stok',
                                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                                'roles' => ['admin_gudang', 'pemilik', 'pekerja_gudang']
                            ],
                        ]
                    ],

                    // 3. Group: KASIR (Kasir, Pemilik)
                    [
                        'type' => 'group',
                        'label' => 'Kasir',
                        'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', // Cart icon
                        'id' => 'kasir',
                        'roles' => ['kasir', 'pemilik'],
                        'items' => [
                            [
                                'route' => 'pos.index',
                                'label' => 'Point of Sale',
                                'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                                'roles' => ['kasir', 'pemilik']
                            ],
                            [
                                'route' => 'transactions.index',
                                'label' => 'Riwayat Transaksi',
                                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                                'roles' => ['kasir', 'pemilik']
                            ],
                            [
                                'route' => 'pos.shifts.index',
                                'label' => 'Laporan Setoran',
                                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                'roles' => ['kasir', 'pemilik']
                            ]
                        ]
                    ],

                    // 4. Pengumuman (Pemilik)
                    [
                        'type' => 'link',
                        'route' => 'announcements.manage.index',
                        'label' => 'Pengumuman',
                        'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
                        'roles' => ['pemilik']
                    ],

                    // 5. Kelola Karyawan (Pemilik)
                    [
                        'type' => 'link',
                        'route' => 'employees.index',
                        'label' => 'Kelola Karyawan',
                        'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                        'roles' => ['pemilik']
                    ],

                    // Kelola Akun (Hanya Pemilik)
                    [
                        'type' => 'link',
                        'route' => 'profile.edit',
                        'label' => 'Kelola Akun',
                        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', // User icon
                        'roles' => ['pemilik']
                    ],

                    // 6. Laporan (Pemilik)
                    [
                        'type' => 'link',
                        'route' => 'reports.index',
                        'label' => 'Laporan',
                        'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'roles' => ['pemilik']
                    ],
                ];
            @endphp

            @foreach($menuItems as $item)
                @if(is_null($item['roles']) || auth()->user()->hasAnyRole($item['roles']))
                    
                    @if($item['type'] == 'link')
                        {{-- SINGLE LINK (Shared Logic) --}}
                        <a href="{{ route($item['route']) }}" class="flex items-center px-3 py-3 rounded-xl transition-all duration-200 group relative mb-1 {{ request()->routeIs($item['route'].'*') ? 'bg-gray-100 text-brand-blue dark:bg-dark-hover dark:text-white' : 'hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-dark-hover dark:hover:text-gray-200' }}">
                            @if(request()->routeIs($item['route'].'*')) <span class="absolute left-0 w-1 h-8 bg-brand-blue rounded-r-full"></span> @endif
                            <svg class="w-6 h-6 min-w-[1.5rem] mr-3 {{ request()->routeIs($item['route'].'*') ? 'text-brand-blue' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"></path></svg>
                            <span x-show="sidebarOpen || mobileSidebarOpen" x-transition.opacity.duration.300ms class="whitespace-nowrap">{{ $item['label'] }}</span>
                        </a>

                    @elseif($item['type'] == 'group')
                        
                        @if(auth()->user()->hasRole('pemilik'))
                            {{-- DROPDOWN GROUP (ONLY FOR PEMILIK) --}}
                            <div x-data="{ open: {{ collect($item['items'])->contains(fn($i) => request()->routeIs($i['route'].'*')) ? 'true' : 'false' }} }" class="mb-1">
                                <button @click="open = !open; if(!sidebarOpen && !mobileSidebarOpen) sidebarOpen = true;" class="w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group relative hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-dark-hover dark:hover:text-gray-200 text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 min-w-[1.5rem] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"></path></svg>
                                        <span x-show="sidebarOpen || mobileSidebarOpen" x-transition.opacity class="whitespace-nowrap font-medium">{{ $item['label'] }}</span>
                                    </div>
                                    <svg x-show="sidebarOpen || mobileSidebarOpen" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                
                                <div x-show="open && (sidebarOpen || mobileSidebarOpen)" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="ml-4 pl-4 border-l border-gray-200 dark:border-gray-700 space-y-1 mt-1">
                                    @foreach($item['items'] as $subItem)
                                        @if(is_null($subItem['roles']) || auth()->user()->hasAnyRole($subItem['roles']))
                                        <a href="{{ route($subItem['route']) }}" class="flex items-center px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs($subItem['route'].'*') ? 'text-brand-blue font-medium bg-blue-50 dark:bg-blue-900/20 dark:text-blue-300' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200' }}">
                                            {{ $subItem['label'] }}
                                        </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            {{-- FLATTENED ITEMS (FOR NON-PEMILIK) --}}
                            @foreach($item['items'] as $subItem)
                                @if(is_null($subItem['roles']) || auth()->user()->hasAnyRole($subItem['roles']))
                                    <a href="{{ route($subItem['route']) }}" class="flex items-center px-3 py-3 rounded-xl transition-all duration-200 group relative mb-1 {{ request()->routeIs($item['route'].'*') ? 'bg-gray-100 text-brand-blue dark:bg-dark-hover dark:text-white' : 'hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-dark-hover dark:hover:text-gray-200' }}">
                                        @if(request()->routeIs($item['route'].'*')) <span class="absolute left-0 w-1 h-8 bg-brand-blue rounded-r-full"></span> @endif
                                        <svg class="w-6 h-6 min-w-[1.5rem] mr-3 {{ request()->routeIs($item['route'].'*') ? 'text-brand-blue' : 'text-gray-400 dark:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $subItem['icon'] }}"></path></svg> 
                                        <span x-show="sidebarOpen || mobileSidebarOpen" x-transition.opacity.duration.300ms class="whitespace-nowrap">{{ $subItem['label'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                        @endif

                    @endif

                @endif
            @endforeach
        </nav>

        <div class="p-4 border-t border-gray-200 overflow-hidden flex-shrink-0 bg-white z-30 dark:bg-dark-card dark:border-gray-800/50">
            <div class="bg-gray-50 rounded-xl p-2 flex items-center transition-all duration-300 min-h-[48px] dark:bg-dark-bg" :class="(sidebarOpen || mobileSidebarOpen) ? 'justify-between px-3' : 'justify-center'">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-8 h-8 min-w-[2rem] rounded-full bg-gradient-to-br from-brand-blue to-purple-600 flex items-center justify-center text-xs text-white font-bold">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div x-show="sidebarOpen || mobileSidebarOpen" x-transition.opacity class="text-xs whitespace-nowrap">
                        <div class="text-gray-800 font-medium truncate max-w-[100px] dark:text-white">{{ Auth::user()->name ?? 'Guest' }}</div>
                        <div class="text-gray-500">{{ Auth::user()->roles->pluck('name')->first() ?? 'User' }}</div>
                    </div>
                </div>
                <form x-show="sidebarOpen || mobileSidebarOpen" x-transition.opacity method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Logout"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg></button>
                </form>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col relative z-10 overflow-hidden h-full">
        <header class="h-16 lg:h-20 flex-shrink-0 flex items-center justify-between px-4 lg:px-8 bg-white/80 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-30 dark:bg-dark-bg/50 dark:border-gray-800/30">
            <div class="flex items-center gap-4">
                <button @click="mobileSidebarOpen = true" class="lg:hidden p-2 text-gray-500 hover:text-brand-blue">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div>
                    <h1 class="text-lg lg:text-xl font-bold text-gray-800 tracking-wide dark:text-white truncate max-w-[150px] md:max-w-none">@yield('page-title', 'Dashboard')</h1>
                    <p class="hidden md:block text-xs text-gray-500 mt-1">Manage your store efficiently</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 lg:gap-4">
                <button @click="toggleTheme()" class="p-2 rounded-full bg-gray-200 text-gray-600 hover:bg-gray-300 transition-colors dark:bg-dark-card dark:text-yellow-400 dark:hover:bg-gray-800" title="Switch Theme">
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
            </div>
        </header>

        <div class="flex-1 overflow-x-hidden overflow-y-auto px-4 lg:px-8 py-4 lg:py-6">
            @if (session('success'))
                <div class="mb-6 bg-brand-green/10 border border-brand-green/20 text-brand-green px-4 py-3 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-12 gap-4 lg:gap-6 pb-10">
                <div class="col-span-12 lg:col-span-9 space-y-6">
                    @yield('content')
                </div>

                <div class="col-span-12 lg:col-span-3 hidden lg:block">
                    @hasSection('right-sidebar')
                        @yield('right-sidebar')
                    @else
                        <div class="bg-white rounded-2xl p-6 border border-gray-200 sticky top-6 dark:bg-dark-card dark:border-gray-800/50">
                            <h3 class="text-gray-800 font-semibold dark:text-white mb-4">Quick Stats</h3>
                            
                            <div class="space-y-4">
                                @if(auth()->user()->hasRole('kasir'))
                                    <div class="block p-4 rounded-xl bg-purple-50 border border-purple-100 dark:bg-purple-900/20 dark:border-purple-800/30">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="text-xs text-purple-600 dark:text-purple-400 font-medium mb-1">Transaksi Saya</div>
                                                <div class="text-xl font-bold text-gray-800 dark:text-white">
                                                    {{ $myTransactionCount ?? 0 }} Cust
                                                </div>
                                                <div class="text-xs text-gray-400 mt-1">Hari ini</div>
                                            </div>
                                            <div class="p-2 bg-purple-100 rounded-lg dark:bg-purple-800/50 text-purple-600 dark:text-purple-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ route('products.index') }}" class="block p-4 rounded-xl bg-orange-50 border border-orange-100 hover:bg-orange-100 transition-colors group dark:bg-brand-orange/10 dark:border-brand-orange/20 dark:hover:bg-brand-orange/20">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="text-xs text-brand-orange font-medium mb-1">Low Stock Alerts</div>
                                                <div class="text-xl font-bold text-gray-800 dark:text-white">
                                                    {{ $globalLowStock ?? 0 }} Products
                                                </div>
                                            </div>
                                            <svg class="w-5 h-5 text-brand-orange opacity-70 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const numBubbles = 12; 
            const speedBase = 0.15;
            if (window.innerWidth <= 1024) return;
            let mouseX = window.innerWidth / 2, mouseY = window.innerHeight / 2;
            let isHovering = false; const bubbles = [];
            const cursorDot = document.createElement('div'); cursorDot.className = 'cursor-dot';
            document.body.appendChild(cursorDot);
            for (let i = 0; i < numBubbles; i++) {
                const bubble = document.createElement('div'); bubble.className = 'cursor-bubble';
                document.body.appendChild(bubble);
                bubbles.push({ x: mouseX, y: mouseY, element: bubble });
            }
            window.addEventListener("mousemove", function(e) {
                mouseX = e.clientX; mouseY = e.clientY;
                cursorDot.style.left = `${mouseX}px`; cursorDot.style.top = `${mouseY}px`;
            });
            function animate() {
                let leaderX = mouseX, leaderY = mouseY;
                bubbles.forEach((bubble, index) => {
                    bubble.x += (leaderX - bubble.x) * speedBase;
                    bubble.y += (leaderY - bubble.y) * speedBase;
                    bubble.element.style.left = `${bubble.x}px`; bubble.element.style.top = `${bubble.y}px`;
                    let scale = (1 - (index / numBubbles) * 0.6) + (isHovering ? 0.3 : 0);
                    bubble.element.style.transform = `translate(-50%, -50%) scale(${scale})`;
                    leaderX = bubble.x; leaderY = bubble.y;
                });
                requestAnimationFrame(animate);
            }
            animate();
            document.body.addEventListener('mouseover', (e) => {
                if (e.target.closest('a, button, input, label, .cursor-pointer')) {
                    isHovering = true; document.body.classList.add('hovering');
                } else {
                    isHovering = false; document.body.classList.remove('hovering');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>