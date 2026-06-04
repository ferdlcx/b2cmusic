@extends('layouts.app')

@section('title', 'Checkout - MusicStore Luxe')

@section('content')
<div class="space-y-10 py-4">
    <!-- Header -->
    <div class="border-b border-slate-200/60 pb-8 space-y-2">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-indigo-600 font-bold block">Proses Transaksi</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tight text-slate-950">Checkout Pesanan</h1>
    </div>

    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form"
          x-data="{ 
              addressId: '{{ $defaultAddress ? $defaultAddress->id : '' }}',
              courier: 'JNE_REG',
              paymentMethod: 'va',
              shippingCost: {{ $shippingCost }},
              shippingLoading: false,
              totalWeight: {{ $cartItems->sum(function($item) { return ($item->product->weight ?: 1000) * $item->quantity; }) }},
              selectedCityId: {{ $defaultAddress ? ($defaultAddress->city_id ?: 'null') : 'null' }},
              couponCode: '',
              discount: 0,
              subtotal: {{ $subtotal }},
              async calculateShipping() {
                  if (!this.selectedCityId) return;
                  
                  this.shippingLoading = true;
                  try {
                      let res = await fetch('{{ route('checkout.shippingCost') }}', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-TOKEN': '{{ csrf_token() }}'
                          },
                          body: JSON.stringify({
                              city_id: this.selectedCityId,
                              weight: this.totalWeight,
                              courier: this.courier
                          })
                      });

                      if (res.ok) {
                          let results = await res.json();
                          if (results && results.length > 0) {
                              let costVal = null;
                              let isYes = this.courier.includes('YES');
                              for (let c of results) {
                                  if (isYes && c.service.toUpperCase().includes('YES')) {
                                      costVal = c.cost[0].value;
                                      break;
                                  } else if (!isYes && c.service.toUpperCase().includes('REG')) {
                                      costVal = c.cost[0].value;
                                      break;
                                  }
                              }
                              if (costVal === null) {
                                  costVal = results[0].cost[0].value;
                              }
                              this.shippingCost = costVal;
                          }
                      }
                  } catch (e) {
                      console.error('Shipping calculate error:', e);
                  } finally {
                      this.shippingLoading = false;
                  }
              },
              applyCoupon() {
                  let code = this.couponCode.toUpperCase().trim();
                  if (code === 'MUSIC10') {
                      this.discount = Math.min(this.subtotal * 0.1, 1000000);
                      alert('Kupon MUSIC10 Berhasil Diterapkan (Diskon 10%)!');
                  } else if (code === 'HEBATSOUND') {
                      this.discount = Math.min(100000, this.subtotal);
                      alert('Kupon HEBATSOUND Berhasil Diterapkan (Diskon Rp 100.000)!');
                  } else {
                      this.discount = 0;
                      alert('Kupon tidak valid atau kadaluarsa.');
                  }
              }
          }">
        @csrf
        
        <input type="hidden" name="address_id" :value="addressId" />
        <input type="hidden" name="courier" :value="courier" />
        <input type="hidden" name="payment_method" :value="paymentMethod" />
        <input type="hidden" name="coupon_code" :value="couponCode" />

        <div class="grid gap-10 lg:grid-cols-[1fr_400px]">
            <!-- Left Side: Shipping Address, Courier, Payment Method -->
            <div class="space-y-8">
                <!-- Address Section -->
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-8 shadow-sm space-y-6">
                    <div class="flex items-center gap-2 pb-4 border-b border-slate-100">
                        <i data-lucide="map-pin" class="w-5 h-5 text-indigo-600"></i>
                        <h3 class="font-display text-lg font-bold uppercase tracking-tight text-slate-950">Alamat Pengiriman</h3>
                    </div>

                    @if($addresses->isEmpty())
                        <div class="p-5 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 text-xs space-y-4">
                            <p class="font-bold flex items-center gap-2">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i> Anda belum memiliki alamat pengiriman tersimpan.
                            </p>
                            <p>Silakan buat alamat baru di bawah ini untuk melanjutkan proses checkout.</p>
                        </div>
                        
                        <!-- Simple creation of default address inline -->
                        <div class="border border-slate-200 rounded-2xl p-6 space-y-4">
                            <span class="font-bold text-slate-800 text-xs block">Buat Alamat Pengiriman Baru</span>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Label Alamat</label>
                                    <input type="text" name="new_address_label" value="Rumah" placeholder="Rumah/Kantor" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Nama Penerima</label>
                                    <input type="text" name="new_address_name" value="{{ auth()->user()->name }}" placeholder="Nama Penerima" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">No. Telepon</label>
                                    <input type="text" name="new_address_phone" value="{{ auth()->user()->phone }}" placeholder="No. Telepon" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kode Pos</label>
                                    <input type="text" name="new_address_postal_code" placeholder="Kode Pos" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600" />
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Alamat Lengkap</label>
                                <textarea name="new_address_detail" rows="2" placeholder="Nama Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600"></textarea>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kota</label>
                                    <input type="text" name="new_address_city" placeholder="Kota" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600" />
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Provinsi</label>
                                    <input type="text" name="new_address_province" placeholder="Provinsi" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600" />
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($addresses as $addr)
                                <div @click="addressId = '{{ $addr->id }}'; selectedCityId = '{{ $addr->city_id }}'; calculateShipping()" 
                                     :class="addressId == '{{ $addr->id }}' ? 'border-indigo-600 bg-indigo-50/20' : 'border-slate-200/80'"
                                     class="flex gap-4 p-5 border-2 rounded-2xl cursor-pointer hover:bg-slate-50/50 transition duration-300">
                                    <div class="pt-0.5">
                                        <div :class="addressId == '{{ $addr->id }}' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300 bg-white'" 
                                             class="w-4 h-4 rounded-full border flex items-center justify-center transition">
                                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-900">{{ $addr->label }}</span>
                                            @if($addr->is_default)
                                                <span class="text-[0.55rem] uppercase tracking-wider bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded font-bold">Default</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-700 font-semibold">{{ $addr->name }} ({{ $addr->phone }})</p>
                                        <p class="text-xs text-slate-500 leading-relaxed font-normal">{{ $addr->address }}, {{ $addr->city }}, {{ $addr->province }}, {{ $addr->postal_code }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Courier Section -->
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-8 shadow-sm space-y-6">
                    <div class="flex items-center gap-2 pb-4 border-b border-slate-100">
                        <i data-lucide="truck" class="w-5 h-5 text-indigo-600"></i>
                        <h3 class="font-display text-lg font-bold uppercase tracking-tight text-slate-950">Metode Pengiriman (Kurir)</h3>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <!-- JNE OKE -->
                        <label class="block p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-600 transition flex items-center justify-between"
                                :class="courier === 'JNE_REG' ? 'border-indigo-600 bg-indigo-50/50 ring-1 ring-indigo-600' : ''">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="courier" value="JNE_REG" x-model="courier" class="text-indigo-600 focus:ring-indigo-600" @change="calculateShipping()">
                                <div>
                                    <span class="block text-sm font-bold">JNE Reguler</span>
                                    <span class="block text-xs text-slate-500">Estimasi 2-3 hari</span>
                                </div>
                            </div>
                        </label>

                        <!-- JNE YES -->
                        <label class="block p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-600 transition flex items-center justify-between"
                                :class="courier === 'JNE_YES' ? 'border-indigo-600 bg-indigo-50/50 ring-1 ring-indigo-600' : ''">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="courier" value="JNE_YES" x-model="courier" class="text-indigo-600 focus:ring-indigo-600" @change="calculateShipping()">
                                <div>
                                    <span class="block text-sm font-bold">JNE YES</span>
                                    <span class="block text-xs text-slate-500">Yakin Esok Sampai</span>
                                </div>
                            </div>
                        </label>

                        <!-- JNT -->
                        <label class="block p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-600 transition flex items-center justify-between"
                                :class="courier === 'JNT' ? 'border-indigo-600 bg-indigo-50/50 ring-1 ring-indigo-600' : ''">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="courier" value="JNT" x-model="courier" class="text-indigo-600 focus:ring-indigo-600" @change="calculateShipping()">
                                <div>
                                    <span class="block text-sm font-bold">J&T Express</span>
                                    <span class="block text-xs text-slate-500">Estimasi 2-4 hari</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Payment Method Section -->
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-8 shadow-sm space-y-6">
                    <div class="flex items-center gap-2 pb-4 border-b border-slate-100">
                        <i data-lucide="wallet" class="w-5 h-5 text-indigo-600"></i>
                        <h3 class="font-display text-lg font-bold uppercase tracking-tight text-slate-950">Metode Pembayaran (Simulasi)</h3>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <!-- VA -->
                        <div @click="paymentMethod = 'va'" 
                             :class="paymentMethod === 'va' ? 'border-indigo-600 bg-indigo-50/20' : 'border-slate-200/80'"
                             class="p-5 border-2 rounded-2xl flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition duration-300">
                            <div class="flex items-center gap-3">
                                <i data-lucide="landmark" class="w-5 h-5 text-slate-600"></i>
                                <span class="font-bold text-slate-900 text-sm">Virtual Account (VA)</span>
                            </div>
                            <div :class="paymentMethod === 'va' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300 bg-white'" class="w-4 h-4 rounded-full border flex items-center justify-center transition">
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            </div>
                        </div>

                        <!-- QRIS -->
                        <div @click="paymentMethod = 'qris'" 
                             :class="paymentMethod === 'qris' ? 'border-indigo-600 bg-indigo-50/20' : 'border-slate-200/80'"
                             class="p-5 border-2 rounded-2xl flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition duration-300">
                            <div class="flex items-center gap-3">
                                <i data-lucide="qr-code" class="w-5 h-5 text-slate-600"></i>
                                <span class="font-bold text-slate-900 text-sm">QRIS (Gopay/OVO)</span>
                            </div>
                            <div :class="paymentMethod === 'qris' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300 bg-white'" class="w-4 h-4 rounded-full border flex items-center justify-center transition">
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            </div>
                        </div>

                        <!-- Credit Card -->
                        <div @click="paymentMethod = 'credit_card'" 
                             :class="paymentMethod === 'credit_card' ? 'border-indigo-600 bg-indigo-50/20' : 'border-slate-200/80'"
                             class="p-5 border-2 rounded-2xl flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition duration-300">
                            <div class="flex items-center gap-3">
                                <i data-lucide="credit-card" class="w-5 h-5 text-slate-600"></i>
                                <span class="font-bold text-slate-900 text-sm">Kartu Kredit / Debit</span>
                            </div>
                            <div :class="paymentMethod === 'credit_card' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300 bg-white'" class="w-4 h-4 rounded-full border flex items-center justify-center transition">
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            </div>
                        </div>

                        <!-- E-Wallet -->
                        <div @click="paymentMethod = 'ewallet'" 
                             :class="paymentMethod === 'ewallet' ? 'border-indigo-600 bg-indigo-50/20' : 'border-slate-200/80'"
                             class="p-5 border-2 rounded-2xl flex items-center justify-between cursor-pointer hover:bg-slate-50/50 transition duration-300">
                            <div class="flex items-center gap-3">
                                <i data-lucide="smartphone" class="w-5 h-5 text-slate-600"></i>
                                <span class="font-bold text-slate-900 text-sm">E-Wallet (Dana/LinkAja)</span>
                            </div>
                            <div :class="paymentMethod === 'ewallet' ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300 bg-white'" class="w-4 h-4 rounded-full border flex items-center justify-center transition">
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary & Coupon -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-8 shadow-sm space-y-6">
                    <h3 class="font-display text-lg font-black uppercase tracking-tight text-slate-950 pb-4 border-b border-slate-100">Item Pesanan</h3>
                    
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between items-center gap-4 py-2 border-b border-slate-50 last:border-0 font-semibold text-xs">
                                <div class="min-w-0">
                                    <span class="text-slate-900 block truncate uppercase">{{ $item->product->name }}</span>
                                    <span class="text-[0.6rem] text-slate-400 block mt-0.5">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                                <span class="text-slate-900 flex-shrink-0">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Coupon Input (simulated in Alpine) -->
                    <div class="pt-4 border-t border-slate-100 space-y-2.5">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kupon Promo</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="couponCode" placeholder="Kode Kupon" 
                                class="min-w-0 flex-1 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white" />
                            <button type="button" @click="applyCoupon()" class="px-4 py-2.5 bg-indigo-600 text-white text-xs font-semibold rounded-xl hover:bg-indigo-700 transition">
                                Gunakan
                            </button>
                        </div>
                        <span class="text-[0.6rem] text-slate-400 block leading-relaxed">Gunakan: <strong class="text-slate-600">MUSIC10</strong> (Diskon 10%), <strong class="text-slate-600">HEBATSOUND</strong> (Diskon Rp100k)</span>
                    </div>

                    <!-- Totals Breakdown -->
                    <div class="pt-4 border-t border-slate-100 space-y-4 text-xs font-semibold text-slate-500">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="text-slate-900 font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Biaya Pengiriman</span>
                            <span class="font-bold text-slate-800" x-show="!shippingLoading">
                                Rp <span x-text="new Intl.NumberFormat('id-ID').format(shippingCost)"></span>
                            </span>
                            <span class="font-bold text-slate-400 text-xs flex items-center gap-2" x-show="shippingLoading" style="display: none;">
                                <i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i> Menghitung...
                            </span>
                        </div>
                        <div class="flex justify-between text-emerald-600" x-show="discount > 0" style="display: none;">
                            <span>Diskon Kupon</span>
                            <span class="font-bold" x-text="'-Rp ' + discount.toLocaleString('id-ID')">-Rp 0</span>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-100 flex justify-between items-end">
                            <span class="text-slate-950 font-bold">Total Pembayaran</span>
                            <span class="text-2xl font-black text-indigo-600" x-text="'Rp ' + (subtotal + shippingCost - discount).toLocaleString('id-ID')">
                                Rp {{ number_format($subtotal + 25000, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full py-4.5 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-sm tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300">
                        Buat Pesanan Sekarang
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
