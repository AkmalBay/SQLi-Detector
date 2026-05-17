@extends('layouts.app')

@section('page-title', 'Point of Sale')

@section('content')
<div class="flex flex-col lg:flex-row gap-6 pb-24 lg:pb-0 h-full lg:h-[calc(100vh-140px)]" x-data="posSystem()">
    
    <div class="w-full lg:w-2/3 flex flex-col gap-4 overflow-y-auto lg:overflow-visible">
        
        <div class="bg-white dark:bg-dark-card p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="relative">
                <input type="text" 
                       id="product-search"
                       class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-gray-700/50 focus:border-brand-blue focus:ring-0 transition-colors text-gray-800 dark:text-white"
                       placeholder="Cari Produk (SKU / Nama) atau Scan Barcode"
                       autofocus>
                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <!-- <span class="px-2 py-1 bg-brand-blue/10 text-brand-blue text-xs font-bold rounded-lg border border-brand-blue/20">F2</span> -->
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
            
            <div id="product-list-container" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse ($products as $product)
                <div class="group bg-white dark:bg-dark-card p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 hover:border-brand-blue/50 dark:hover:border-brand-blue/50 transition-all cursor-pointer relative overflow-hidden product-card"
                     data-product-id="{{ $product->id }}"
                     data-product-sku="{{ $product->sku }}"
                     data-product-name="{{ $product->name }}"
                     data-product-price="{{ $product->price }}"
                     data-product-stock="{{ $product->stock }}">

                    <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="w-6 h-6 bg-brand-blue rounded-full flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="text-xs text-gray-500 mb-1">{{ $product->sku }}</div>
                        <h4 class="font-medium text-gray-800 dark:text-white leading-tight line-clamp-2 h-10">{{ $product->name }}</h4>
                    </div>

                    <div class="flex justify-between items-end">
                        <div class="font-bold text-brand-blue">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        <div class="text-xs {{ $product->stock <= 5 ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                            Stok: {{ $product->stock }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full flex flex-col items-center justify-center py-10 text-gray-400">
                    <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <p>Tidak ada produk tersedia.</p>
                </div>
                @endforelse
            </div>
            <div id="search-results" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 hidden"></div>

        </div>
    </div>

    <!-- Cart Drawer for Mobile / Sidebar for Desktop -->
    <div 
        class="fixed inset-0 z-40 lg:relative lg:inset-auto lg:z-0 flex flex-col lg:w-1/3 transition-transform duration-300 transform"
        :class="showCartMobile ? 'translate-y-0' : 'translate-y-full lg:translate-y-0'"
    >
        <!-- Overlay for Mobile Cart -->
        <div x-show="showCartMobile" @click="showCartMobile = false" class="lg:hidden absolute inset-0 bg-black/50 -translate-y-full"></div>
        
        <div class="bg-white dark:bg-dark-card rounded-t-3xl lg:rounded-2xl shadow-2xl lg:shadow-xl flex flex-col h-[80vh] lg:h-full border border-gray-100 dark:border-gray-800 overflow-hidden relative z-50 lg:z-0">
            
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-white/5 lg:hidden">
                <h3 class="font-bold text-gray-800 dark:text-white">Detail Pesanan</h3>
                <button @click="showCartMobile = false" class="p-2 text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
            
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-white/5">
                <div>
                    <h2 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Current Bill
                    </h2>
                    <p class="text-xs text-gray-500">Shift #{{ $activeShift->id }} • {{ Auth::user()->name }}</p>
                </div>
                <a href="{{ route('pos.shift.close.form') }}" class="text-xs text-red-500 hover:text-red-600 font-medium px-2 py-1 hover:bg-red-50 rounded transition-colors">
                    Close Shift
                </a>
            </div>

            <div class="flex-1 overflow-y-auto p-2 custom-scrollbar space-y-2" id="cart-items-container">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-gray-400">
                        <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <p class="text-sm">Keranjang kosong</p>
                        <p class="text-xs opacity-70">Scan barang atau pilih dari list</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 group border border-transparent hover:border-gray-100 dark:hover:border-gray-700 transition-all">
                        <div class="flex-1 overflow-hidden mr-3">
                            <h5 class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" x-text="item.name"></h5>
                            <div class="text-xs text-gray-500" x-text="'Rp ' + formatPrice(item.price)"></div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="flex items-center bg-gray-100 dark:bg-dark-bg rounded-lg p-1">
                                <button @click="updateQty(item.id, -1)" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-red-500 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                </button>
                                <input type="text" class="w-8 text-center bg-transparent text-sm font-bold text-gray-800 dark:text-white focus:outline-none p-0 border-none" :value="item.quantity" readonly>
                                <button @click="updateQty(item.id, 1)" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-green-500 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                            <div class="text-sm font-bold text-gray-800 dark:text-white text-right min-w-[80px]" x-text="'Rp ' + formatPrice(item.price * item.quantity)"></div>
                            <button @click="removeFromCart(item.id)" class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-white/5 border-t border-gray-100 dark:border-gray-800">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between items-center text-gray-500 text-sm">
                        <span>Subtotal</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200" x-text="'Rp ' + formatPrice(subtotal)"></span>
                    </div>
                    <div class="flex justify-between items-center text-gray-500 text-sm">
                        <span>Diskon</span>
                        <span class="font-medium text-green-500">- Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span class="font-bold text-lg text-gray-800 dark:text-white">TOTAL</span>
                        <span class="font-bold text-2xl text-brand-blue" x-text="'Rp ' + formatPrice(subtotal)"></span>
                    </div>
                </div>

                <button @click="openPaymentModal()" 
                        :disabled="cart.length === 0"
                        class="w-full py-4 rounded-xl bg-brand-blue hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-lg shadow-lg shadow-brand-blue/30 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                    <span>Bayar Sekarang</span>
                    <!-- <span class="bg-white/20 text-xs px-2 py-0.5 rounded">F9</span> -->
                </button>
            </div>
        </div>
    </div>

    <div x-show="showPaymentModal" style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center px-4" x-cloak>
        
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showPaymentModal = false"></div>

        <div class="bg-white dark:bg-dark-card w-full max-w-2xl rounded-3xl shadow-2xl relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-white/5">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">Pembayaran</h3>
                <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-8 flex flex-col md:flex-row gap-8 overflow-y-auto">
                
                <div class="md:w-5/12 space-y-6">
                    <div class="text-center p-6 bg-brand-blue/5 rounded-2xl border border-brand-blue/10">
                        <div class="text-sm text-gray-500 mb-1">Total Tagihan</div>
                        <div class="text-3xl font-bold text-brand-blue" x-text="'Rp ' + formatPrice(subtotal)"></div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total Bayar</span>
                            <span class="font-medium dark:text-white" x-text="'Rp ' + formatPrice(payCash + payNonCash)"></span>
                        </div>
                        <div class="flex justify-between text-sm pt-3 border-t border-gray-100 dark:border-gray-800">
                            <span class="text-gray-500">Kembalian</span>
                            <span class="font-bold text-lg" :class="change < 0 ? 'text-red-500' : 'text-green-500'" x-text="change < 0 ? 'Kurang Rp ' + formatPrice(Math.abs(change)) : 'Rp ' + formatPrice(change)"></span>
                        </div>
                    </div>
                </div>

                <div class="md:w-7/12 space-y-5">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">💵 Tunai (Cash)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                            <input type="number" x-model.number="payCash" 
                                   class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-gray-700 focus:border-brand-blue focus:ring-0 text-lg font-bold text-gray-800 dark:text-white"
                                   placeholder="0">
                        </div>
                        <div class="flex gap-2 mt-2">
                            <button @click="payCash = subtotal" class="px-3 py-1 text-xs bg-gray-100 dark:bg-white/10 rounded-lg hover:bg-gray-200 transition-colors">Uang Pas</button>
                            <button @click="payCash = 50000" class="px-3 py-1 text-xs bg-gray-100 dark:bg-white/10 rounded-lg hover:bg-gray-200 transition-colors">50k</button>
                            <button @click="payCash = 100000" class="px-3 py-1 text-xs bg-gray-100 dark:bg-white/10 rounded-lg hover:bg-gray-200 transition-colors">100k</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">💳 Non-Tunai (Split Bill)</label>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <select x-model="nonCashMethod" class="col-span-1 pl-2 pr-6 py-3 rounded-xl bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-gray-700 text-sm focus:border-brand-blue focus:ring-0">
                                <option value="QRIS">QRIS</option>
                                <option value="DEBIT">Debit</option>
                                <option value="CREDIT">Credit</option>
                                <option value="TRANSFER">Transfer</option>
                            </select>
                            <div class="col-span-2 relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                <input type="number" x-model.number="payNonCash" 
                                       class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-gray-700 focus:border-brand-blue focus:ring-0 font-bold text-gray-800 dark:text-white"
                                       placeholder="Nominal">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="px-8 py-6 bg-gray-50 dark:bg-white/5 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
                <button @click="showPaymentModal = false" class="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/10 font-bold">Batal</button>
                <button @click="processPayment()" 
                        :disabled="isProcessing || change < 0"
                        class="px-8 py-3 rounded-xl bg-brand-blue hover:bg-blue-600 text-white font-bold shadow-lg shadow-brand-blue/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center gap-2">
                    <svg x-show="isProcessing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="isProcessing ? 'Memproses...' : 'Proses Pembayaran'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Cart Button for Mobile -->
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 lg:hidden">
        <button @click="showCartMobile = true" class="flex items-center gap-3 bg-brand-blue text-white px-6 py-4 rounded-2xl font-bold shadow-2xl shadow-brand-blue/40 transform active:scale-95 transition-transform">
            <div class="relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <div x-show="cart.length > 0" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 rounded-full text-[10px] flex items-center justify-center border-2 border-brand-blue" x-text="cart.length"></div>
            </div>
            <span>Lihat Keranjang</span>
            <span class="text-white/70" x-text="'Rp ' + formatPrice(subtotal)"></span>
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function posSystem() {
        return {
            cart: [],
            products: @json($products),
            showPaymentModal: false,
            showCartMobile: false,
            payCash: 0,
            payNonCash: 0,
            nonCashMethod: 'QRIS',
            isProcessing: false,
            
            // 1. Computed Properties
            get subtotal() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },
            
            get change() {
                // Pastikan angka tidak NaN
                let cash = parseFloat(this.payCash) || 0;
                let nonCash = parseFloat(this.payNonCash) || 0;
                return (cash + nonCash) - this.subtotal;
            },

            // 2. Helper Format Rupiah
            formatPrice(value) {
                return new Intl.NumberFormat('id-ID').format(value);
            },

            // 3. Logic Tambah ke Keranjang
            addToCart(id, sku, name, price, stock) {
                // Cek Stok Client Side (Optimistic)
                if (stock < 1) {
                    alert('Stok Fisik Habis!'); 
                    return;
                }

                let existingItem = this.cart.find(i => i.id === id);
                
                if (existingItem) {
                    // Jika barang sudah ada, tambah qty +1
                    this.updateQty(id, 1);
                } else {
                    // Jika barang baru
                    this.cart.push({ 
                        id: id, 
                        sku: sku, 
                        name: name, 
                        price: parseFloat(price), 
                        quantity: 1, 
                        maxStock: stock 
                    });
                    
                    // Validasi ke Server
                    this.checkStockServer(id, 1);
                }
                
                // Efek visual kecil (Opsional/Console log)
                console.log('Added:', name);
            },

            // 4. Update Quantity (+ / -)
            updateQty(id, change) {
                let item = this.cart.find(i => i.id === id);
                if (!item) return;

                let newQty = item.quantity + change;

                if (newQty <= 0) {
                    this.removeFromCart(id);
                } else {
                    // Cek Max Stock Client Side
                    if (change > 0 && newQty > item.maxStock) {
                        alert('Stok tidak mencukupi! Sisa: ' + item.maxStock);
                        return;
                    }
                    
                    // Update Data
                    item.quantity = newQty;
                    
                    // Validasi Server (Penting untuk Strict Mode)
                    this.checkStockServer(id, newQty, item);
                }
            },

            removeFromCart(id) {
                this.cart = this.cart.filter(i => i.id !== id);
            },
            
            clearCart() {
                if(confirm('Batalkan dan hapus semua item di keranjang?')) {
                    this.cart = [];
                }
            },

            // 5. Cek Stok ke Server (AJAX)
            checkStockServer(id, qty, itemRef = null) {
                 fetch(`{{ route('pos.check.stock', '') }}/${id}`, { // Fix Route URL
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ quantity: qty })
                })
                .then(res => {
                    if(!res.ok) throw new Error('Stok Habis');
                    return res.json();
                })
                .then(data => {
                    if (!data.available) {
                        alert(data.message);
                        // Jika gagal, kembalikan qty ke sebelumnya
                        if(itemRef) itemRef.quantity = qty - 1; 
                        else this.removeFromCart(id);
                    }
                })
                .catch(err => {
                    console.error('Stock Check Error:', err);
                    // Revert jika error server
                    if(itemRef && qty > 1) itemRef.quantity = qty - 1;
                });
            },

            // 6. Modal & Pembayaran
            openPaymentModal() {
                this.payCash = 0;
                this.payNonCash = 0;
                this.showPaymentModal = true;
                
                // Otomatis fokus ke input cash setelah modal terbuka
                setTimeout(() => {
                    // Cari input pertama di modal jika ada ID, atau biarkan user klik manual
                }, 100);
            },

            processPayment() {
                if (this.change < 0) {
                    alert('Uang pembayaran kurang!');
                    return;
                }
                
                this.isProcessing = true;
                
                let payments = [];
                if (this.payCash > 0) payments.push({ method: 'CASH', amount: this.payCash });
                if (this.payNonCash > 0) payments.push({ method: this.nonCashMethod, amount: this.payNonCash });

                let payload = {
                    total_amount: this.subtotal,
                    amount_paid: (this.payCash || 0) + (this.payNonCash || 0),
                    change: this.change,
                    cart_items: this.cart,
                    payments: payments
                };

                fetch("{{ route('pos.transaction.store') }}", {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    this.isProcessing = false;
                    if (data.success) {
                        this.showPaymentModal = false;
                        this.cart = []; // Kosongkan keranjang
                        
                        // Buka Invoice di Tab Baru
                        let invoiceUrl = "{{ route('transactions.invoice', ':id') }}";
                        invoiceUrl = invoiceUrl.replace(':id', data.transaction_id);
                        window.open(invoiceUrl, '_blank');
                        
                    } else {
                        alert('Gagal: ' + (data.error || 'Unknown Error'));
                    }
                })
                .catch(err => {
                    this.isProcessing = false;
                    alert('Terjadi kesalahan jaringan/server.');
                    console.error(err);
                });
            },

            // 7. Inisialisasi & Event Listeners
            init() {
                // Trik: Simpan scope Alpine ke window agar bisa dipanggil dari HTML string hasil Search
                window.posScope = this;

                // Event listener untuk product cards (menggunakan data attributes untuk XSS protection)
                document.querySelectorAll('.product-card').forEach(card => {
                    card.addEventListener('click', () => {
                        const id = parseInt(card.dataset.productId);
                        const sku = card.dataset.productSku;
                        const name = card.dataset.productName;
                        const price = parseFloat(card.dataset.productPrice);
                        const stock = parseInt(card.dataset.productStock);
                        this.addToCart(id, sku, name, price, stock);
                    });
                });

                // Keyboard Shortcuts
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'F2') {
                        e.preventDefault();
                        document.getElementById('product-search').focus();
                    }
                    if (e.key === 'F9' && this.cart.length > 0) {
                        e.preventDefault();
                        this.openPaymentModal();
                    }
                    if (e.key === 'Escape') {
                         if(this.showPaymentModal) this.showPaymentModal = false;
                    }
                });

                // AJAX Search Listener
                let searchTimeout;
                const searchInput = document.getElementById('product-search');
                
                searchInput.addEventListener('keyup', (e) => {
                    clearTimeout(searchTimeout);
                    let keyword = e.target.value;
                    const defaultList = document.getElementById('product-list-container');
                    const searchResults = document.getElementById('search-results');

                    if (keyword.length < 3) {
                        defaultList.classList.remove('hidden');
                        searchResults.classList.add('hidden');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        fetch(`{{ route('pos.search') }}?q=${keyword}`)
                            .then(res => res.json())
                            .then(data => {
                                defaultList.classList.add('hidden');
                                searchResults.classList.remove('hidden');
                                searchResults.innerHTML = ''; // Clear

                                if (data.length === 0) {
                                    searchResults.innerHTML = '<div class="col-span-full text-center text-gray-500 py-10">Produk tidak ditemukan</div>';
                                    return;
                                }

                                data.forEach(p => {
                                    // Escape HTML untuk mencegah XSS
                                    function escapeHtml(text) {
                                        const map = {
                                            '&': '&amp;',
                                            '<': '&lt;',
                                            '>': '&gt;',
                                            '"': '&quot;',
                                            "'": '&#039;'
                                        };
                                        return text.replace(/[&<>"']/g, m => map[m]);
                                    }

                                    let safeName = escapeHtml(p.name);
                                    let safeSku = escapeHtml(p.sku);

                                    // PERBAIKAN UTAMA:
                                    // Menggunakan window.posScope.addToCart(...) dengan proper escaping
                                    let html = `
                                    <div class="group bg-white dark:bg-dark-card p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 hover:border-brand-blue/50 transition-all cursor-pointer relative"
                                         onclick="window.posScope.addToCart(${p.id}, '${safeSku}', '${safeName.replace(/'/g, "\\'")}', ${p.price}, ${p.stock})">

                                        <div class="mb-3">
                                            <div class="text-xs text-gray-500 mb-1">${safeSku}</div>
                                            <h4 class="font-medium text-gray-800 dark:text-white line-clamp-2 h-10">${safeName}</h4>
                                        </div>
                                        <div class="flex justify-between items-end">
                                            <div class="font-bold text-brand-blue">Rp ${new Intl.NumberFormat('id-ID').format(p.price)}</div>
                                            <div class="text-xs text-gray-500">Stok: ${p.stock}</div>
                                        </div>
                                    </div>`;

                                    searchResults.insertAdjacentHTML('beforeend', html);
                                });
                            });
                    }, 300);
                });
            }
        }
    }
</script>
@endsection