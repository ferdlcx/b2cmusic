@extends('layouts.app')

@section('title', 'Checkout - MusicStore Luxe')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-8">
        <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Proses Transaksi</span>
        <h1 class="text-4xl md:text-5xl font-black uppercase tracking-[-0.04em] text-slate-950 mt-3">Checkout Pesanan</h1>
    </div>

    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
        @csrf
        <div class="grid gap-10 lg:grid-cols-[1fr_400px]">
            <!-- Left Side: Shipping Address, Courier, Payment Method -->
            <div class="space-y-8">
                <!-- Address Section -->
                <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <h3 class="text-xl font-black uppercase tracking-tight text-slate-950">Alamat Pengiriman</h3>
                    </div>

                    @if($addresses->isEmpty())
                        <div class="p-6 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 text-sm space-y-4">
                            <p class="font-bold">Anda belum menambahkan alamat pengiriman.</p>
                            <p>Silakan buat alamat default terlebih dahulu untuk melanjutkan checkout.</p>
                        </div>
                        
                        <!-- Simple creation of default address inline to avoid blocking! -->
                        <div class="border border-slate-200 rounded-2xl p-6 space-y-4">
                            <span class="font-bold text-slate-800 text-sm block">Buat Alamat Pengiriman Baru</span>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold block">Label Alamat</label>
                                    <input type="text" name="new_address_label" value="Rumah" placeholder="Rumah/Kantor" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold block">Nama Penerima</label>
                                    <input type="text" name="new_address_name" value="{{ auth()->user()->name }}" placeholder="Nama Penerima" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold block">No. Telepon Penerima</label>
                                    <input type="text" name="new_address_phone" value="{{ auth()->user()->phone }}" placeholder="No. Telepon" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold block">Kode Pos</label>
                                    <input type="text" name="new_address_postal_code" placeholder="Kode Pos" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs" />
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold block">Alamat Lengkap</label>
                                <textarea name="new_address_detail" rows="3" placeholder="Nama Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs"></textarea>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold block">Kota</label>
                                    <input type="text" name="new_address_city" placeholder="Kota" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold block">Provinsi</label>
                                    <input type="text" name="new_address_province" placeholder="Provinsi" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs" />
                                </div>
                            </div>
                            <!-- Helper to submit addresses if empty -->
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const form = document.getElementById('checkout-form');
                                    form.addEventListener('submit', function(e) {
                                        // We will handle address creation on backend if address_id is empty/absent
                                    });
                                });
                            </script>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($addresses as $addr)
                                <label class="flex gap-4 p-4 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50/50 transition">
                                    <div class="pt-1">
                                        <input type="radio" name="address_id" value="{{ $addr->id }}" {{ $addr->id === $defaultAddress->id ? 'checked' : '' }} class="text-slate-950 focus:ring-slate-950" />
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-slate-900">{{ $addr->label }}</span>
                                            @if($addr->is_default)
                                                <span class="text-[0.6rem] uppercase tracking-wider bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-bold">Default</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-700 font-medium">{{ $addr->name }} ({{ $addr->phone }})</p>
                                        <p class="text-xs text-slate-500 leading-relaxed">{{ $addr->address }}, {{ $addr->city }}, {{ $addr->province }}, {{ $addr->postal_code }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Courier Section -->
                <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-6">
                    <h3 class="text-xl font-black uppercase tracking-tight text-slate-950 pb-4 border-b border-slate-100">Metode Pengiriman (Kurir)</h3>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <label class="p-4 border border-slate-200 rounded-2xl flex flex-col justify-between cursor-pointer hover:bg-slate-50/50 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-sm">JNE OKE</span>
                                <input type="radio" name="courier" value="JNE_REG" checked class="text-slate-950 focus:ring-slate-950" />
                            </div>
                            <div class="mt-4">
                                <span class="text-[0.65rem] uppercase tracking-wider text-slate-400 block font-bold">Standard (2-4 hari)</span>
                                <span class="text-sm font-black text-slate-900 mt-1 block">Rp 25.000</span>
                            </div>
                        </label>

                        <label class="p-4 border border-slate-200 rounded-2xl flex flex-col justify-between cursor-pointer hover:bg-slate-50/50 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-sm">JNE YES</span>
                                <input type="radio" name="courier" value="JNE_YES" class="text-slate-950 focus:ring-slate-950" />
                            </div>
                            <div class="mt-4">
                                <span class="text-[0.65rem] uppercase tracking-wider text-slate-400 block font-bold">Express (1 hari)</span>
                                <span class="text-sm font-black text-slate-900 mt-1 block">Rp 40.000</span>
                            </div>
                        </label>

                        <label class="p-4 border border-slate-200 rounded-2xl flex flex-col justify-between cursor-pointer hover:bg-slate-50/50 transition">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-sm">J&T Express</span>
                                <input type="radio" name="courier" value="JNT" class="text-slate-950 focus:ring-slate-950" />
                            </div>
                            <div class="mt-4">
                                <span class="text-[0.65rem] uppercase tracking-wider text-slate-400 block font-bold">Reguler (2-3 hari)</span>
                                <span class="text-sm font-black text-slate-900 mt-1 block">Rp 25.000</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Payment Method Section -->
                <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-6">
                    <h3 class="text-xl font-black uppercase tracking-tight text-slate-950 pb-4 border-b border-slate-100">Metode Pembayaran (Simulasi)</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="p-4 border border-slate-200 rounded-2xl flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                                </svg>
                                <span class="font-bold text-slate-900 text-sm">Virtual Account (VA)</span>
                            </div>
                            <input type="radio" name="payment_method" value="va" checked class="text-slate-950 focus:ring-slate-950" />
                        </label>

                        <label class="p-4 border border-slate-200 rounded-2xl flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM13.5 16.5a.75.75 0 01.75-.75h3a.75.75 0 010 1.5h-3a.75.75 0 01-.75-.75zM13.5 19.5a.75.75 0 01.75-.75h3a.75.75 0 010 1.5h-3a.75.75 0 01-.75-.75z" />
                                </svg>
                                <span class="font-bold text-slate-900 text-sm">QRIS (Gopay/OVO)</span>
                            </div>
                            <input type="radio" name="payment_method" value="qris" class="text-slate-950 focus:ring-slate-950" />
                        </label>

                        <label class="p-4 border border-slate-200 rounded-2xl flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                </svg>
                                <span class="font-bold text-slate-900 text-sm">Kartu Kredit</span>
                            </div>
                            <input type="radio" name="payment_method" value="credit_card" class="text-slate-950 focus:ring-slate-950" />
                        </label>

                        <label class="p-4 border border-slate-200 rounded-2xl flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-bold text-slate-900 text-sm">E-Wallet (Dana/LinkAja)</span>
                            </div>
                            <input type="radio" name="payment_method" value="ewallet" class="text-slate-950 focus:ring-slate-950" />
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary & Coupon -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-white border border-slate-200 rounded-[32px] p-8 shadow-sm space-y-6">
                    <h3 class="text-xl font-black uppercase tracking-tight text-slate-950 pb-4 border-b border-slate-100">Item Pesanan</h3>
                    
                    <div class="space-y-4 max-h-60 overflow-y-auto">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between items-center gap-4 py-2 border-b border-slate-50 last:border-0">
                                <div class="min-w-0">
                                    <span class="font-bold text-slate-900 text-xs block truncate uppercase">{{ $item->product->name }}</span>
                                    <span class="text-[0.65rem] text-slate-400 block mt-0.5">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                                <span class="text-xs font-black text-slate-900 flex-shrink-0">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Coupon Input -->
                    <div class="pt-4 border-t border-slate-100 space-y-2">
                        <label for="coupon_code" class="text-[0.65rem] uppercase tracking-widest text-slate-500 font-bold block">Kupon Promo</label>
                        <div class="flex gap-2">
                            <input type="text" name="coupon_code" id="coupon_code" placeholder="Kode Kupon (e.g. MUSIC10)" 
                                class="min-w-0 flex-1 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-slate-950" />
                        </div>
                        <span class="text-[0.65rem] text-slate-400 block">Kupon demo: <strong class="text-slate-600">MUSIC10</strong> (Diskon 10%), <strong class="text-slate-600">HEBATSOUND</strong> (Diskon Rp100k)</span>
                    </div>

                    <!-- Totals Breakdown -->
                    <div class="pt-4 border-t border-slate-100 space-y-4 text-sm text-slate-500">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Biaya Pengiriman</span>
                            <span class="font-bold text-slate-900" id="shipping-display">Rp 25.000</span>
                        </div>
                        <div class="flex justify-between text-rose-600 hidden" id="discount-row">
                            <span>Diskon Kupon</span>
                            <span class="font-bold" id="discount-display">-Rp 0</span>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-100 flex justify-between items-end">
                            <span class="text-sm font-bold text-slate-900">Total Pembayaran</span>
                            <span class="text-2xl font-black text-slate-900" id="total-display">Rp {{ number_format($subtotal + 25000, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full py-4 bg-slate-950 text-white rounded-xl font-black uppercase text-xs tracking-[0.22em] hover:bg-slate-800 transition shadow-sm">
                        Buat Pesanan Sekarang
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript to update totals dynamically when courier is selected -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const courierRadios = document.querySelectorAll('input[name="courier"]');
        const shippingDisplay = document.getElementById('shipping-display');
        const totalDisplay = document.getElementById('total-display');
        const subtotal = {{ $subtotal }};
        
        courierRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const cost = this.value === 'JNE_YES' ? 40000 : 25000;
                shippingDisplay.textContent = 'Rp ' + cost.toLocaleString('id-ID');
                
                const finalTotal = subtotal + cost;
                totalDisplay.textContent = 'Rp ' + finalTotal.toLocaleString('id-ID');
            });
        });
    });
</script>
@endsection
