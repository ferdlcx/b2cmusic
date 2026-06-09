@extends('layouts.app')

@section('title', 'Checkout - DjudasMS')

@section('content')
<div class="space-y-12 py-8">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-8 space-y-4">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Penyelesaian Transaksi</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">Checkout <span class="text-gold-500">Pesanan.</span></h1>
    </div>

    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form"
          x-data="{ 
              addressId: '{{ $defaultAddress ? $defaultAddress->id : '' }}',
              courier: 'JNE_REG',
              paymentMethod: 'va',
              packingOption: 'standard',
              packingCost: 0,
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
                  } else if (code === 'DEMODJUDAS') {
                      this.discount = Math.min(this.subtotal * 0.2, 1500000);
                      alert('Kupon DEMODJUDAS Berhasil Diterapkan (Diskon 20%)!');
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
        <input type="hidden" name="packing" :value="packingOption" />

        <div class="grid gap-16 lg:grid-cols-[1fr_400px]">
            <!-- Left Side: Forms -->
            <div class="space-y-12">
                <!-- Address Section -->
                <div class="space-y-6">
                    <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950 pb-4 border-b border-walnut-800/10">Alamat Pengiriman</h3>

                    @if($addresses->isEmpty())
                        <div class="p-6 bg-cream-50 border border-walnut-800/10 text-muted text-sm space-y-4">
                            <p class="font-bold uppercase tracking-widest text-walnut-950">Belum Ada Alamat</p>
                            <p>Silakan buat alamat pengiriman baru untuk melanjutkan checkout.</p>
                        </div>
                        
                        <div class="space-y-4 pt-4">
                            <span class="font-bold text-walnut-900 text-[0.65rem] uppercase tracking-widest block">Buat Alamat Baru</span>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <input type="text" name="new_address_label" value="Rumah" placeholder="Label (Rumah/Kantor)" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                <input type="text" name="new_address_name" value="{{ auth()->user()->name }}" placeholder="Nama Penerima" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                <input type="text" name="new_address_phone" value="{{ auth()->user()->phone }}" placeholder="No. Telepon" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                <input type="text" name="new_address_postal_code" placeholder="Kode Pos" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                            </div>
                            <textarea name="new_address_address" rows="2" placeholder="Detail Alamat" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium"></textarea>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <input type="text" name="new_address_city" placeholder="Kota" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                <input type="text" name="new_address_province" placeholder="Provinsi" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($addresses as $addr)
                                <div @click="addressId = '{{ $addr->id }}'; selectedCityId = '{{ $addr->city_id }}'; calculateShipping()" 
                                     :class="addressId == '{{ $addr->id }}' ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20'"
                                     class="flex gap-4 p-6 border cursor-pointer hover:bg-cream-50 transition duration-300">
                                    <div class="pt-0.5">
                                        <div :class="addressId == '{{ $addr->id }}' ? 'border-gold-500 bg-gold-500' : 'border-walnut-800/30 bg-transparent'" 
                                             class="w-4 h-4 rounded-full border flex items-center justify-center transition">
                                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[0.75rem] font-bold uppercase tracking-widest text-walnut-950">{{ $addr->label }}</span>
                                            @if($addr->is_default)
                                                <span class="text-[0.55rem] uppercase tracking-wider bg-walnut-900 text-gold-500 px-2 py-0.5 font-bold">Utama</span>
                                            @endif
                                        </div>
                                        <p class="text-[0.8rem] text-walnut-900 font-bold">{{ $addr->name }} ({{ $addr->phone }})</p>
                                        <p class="text-[0.75rem] text-muted leading-relaxed font-medium">{{ $addr->address }}, {{ $addr->city }}, {{ $addr->province }}, {{ $addr->postal_code }}</p>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Option for New Address -->
                            <div @click="addressId = ''; selectedCityId = null; shippingCost = 0" 
                                 :class="addressId === '' ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20'"
                                 class="flex gap-4 p-6 border cursor-pointer hover:bg-cream-50 transition duration-300">
                                <div class="pt-0.5">
                                    <div :class="addressId === '' ? 'border-gold-500 bg-gold-500' : 'border-walnut-800/30 bg-transparent'" 
                                         class="w-4 h-4 rounded-full border flex items-center justify-center transition">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                    </div>
                                </div>
                                <div class="space-y-1 w-full">
                                    <span class="text-[0.75rem] font-bold uppercase tracking-widest text-walnut-950 block mb-4">+ Alamat Baru</span>
                                    
                                    <div x-show="addressId === ''" class="pt-2 space-y-4">
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <input type="text" name="new_address_label" placeholder="Label (Rumah/Kantor)" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                            <input type="text" name="new_address_name" placeholder="Nama Penerima" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                            <input type="text" name="new_address_phone" placeholder="No. Telepon" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                            <input type="text" name="new_address_postal_code" placeholder="Kode Pos" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                        </div>
                                        <textarea name="new_address_address" rows="2" placeholder="Detail Alamat" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium"></textarea>
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <input type="text" name="new_address_city" placeholder="Kota" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                            <input type="text" name="new_address_province" placeholder="Provinsi" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Courier Section -->
                <div class="space-y-6 pt-6 border-t border-walnut-800/10">
                    <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950 pb-4 border-b border-walnut-800/10">Layanan Logistik</h3>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <label class="block p-5 border cursor-pointer hover:border-gold-500 transition flex items-center justify-between"
                                :class="courier === 'JNE_REG' ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="courier" value="JNE_REG" x-model="courier" class="text-gold-600 focus:ring-gold-500 bg-cream-50 border-walnut-800/30" @change="calculateShipping()">
                                <div>
                                    <span class="block text-[0.75rem] font-bold uppercase tracking-widest text-walnut-950">Reguler</span>
                                    <span class="block text-[0.65rem] text-muted">2-3 Hari</span>
                                </div>
                            </div>
                        </label>
                        <label class="block p-5 border cursor-pointer hover:border-gold-500 transition flex items-center justify-between"
                                :class="courier === 'JNE_YES' ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="courier" value="JNE_YES" x-model="courier" class="text-gold-600 focus:ring-gold-500 bg-cream-50 border-walnut-800/30" @change="calculateShipping()">
                                <div>
                                    <span class="block text-[0.75rem] font-bold uppercase tracking-widest text-walnut-950">Express</span>
                                    <span class="block text-[0.65rem] text-muted">Esok Sampai</span>
                                </div>
                            </div>
                        </label>
                        <label class="block p-5 border cursor-pointer hover:border-gold-500 transition flex items-center justify-between"
                                :class="courier === 'JNT' ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="courier" value="JNT" x-model="courier" class="text-gold-600 focus:ring-gold-500 bg-cream-50 border-walnut-800/30" @change="calculateShipping()">
                                <div>
                                    <span class="block text-[0.75rem] font-bold uppercase tracking-widest text-walnut-950">Cargo</span>
                                    <span class="block text-[0.65rem] text-muted">Heavy Duty</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Packing Protection Section -->
                <div class="space-y-6 pt-6 border-t border-walnut-800/10">
                    <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950 pb-4 border-b border-walnut-800/10">Proteksi Pengiriman</h3>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <label class="block p-5 border cursor-pointer hover:border-gold-500 transition"
                                :class="packingOption === 'standard' ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="packing" value="standard" x-model="packingOption" @change="packingCost = 0" class="text-gold-600 focus:ring-gold-500 bg-cream-50 border-walnut-800/30">
                                <div>
                                    <span class="block text-[0.75rem] font-bold uppercase tracking-widest text-walnut-950">Standar</span>
                                    <span class="block text-[0.65rem] text-gold-600 font-bold mt-1">Termasuk</span>
                                </div>
                            </div>
                        </label>
                        <label class="block p-5 border cursor-pointer hover:border-gold-500 transition"
                                :class="packingOption === 'bubble_wrap' ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="packing" value="bubble_wrap" x-model="packingOption" @change="packingCost = 15000" class="text-gold-600 focus:ring-gold-500 bg-cream-50 border-walnut-800/30">
                                <div>
                                    <span class="block text-[0.75rem] font-bold uppercase tracking-widest text-walnut-950">Ekstra Aman</span>
                                    <span class="block text-[0.65rem] text-muted font-bold mt-1">+ IDR 15k</span>
                                </div>
                            </div>
                        </label>
                        <label class="block p-5 border cursor-pointer hover:border-gold-500 transition"
                                :class="packingOption === 'wooden_crate' ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="packing" value="wooden_crate" x-model="packingOption" @change="packingCost = 50000" class="text-gold-600 focus:ring-gold-500 bg-cream-50 border-walnut-800/30">
                                <div>
                                    <span class="block text-[0.75rem] font-bold uppercase tracking-widest text-walnut-950">Peti Kayu</span>
                                    <span class="block text-[0.65rem] text-muted font-bold mt-1">+ IDR 50k</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Payment Method Section -->
                <div class="space-y-6 pt-6 border-t border-walnut-800/10">
                    <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950 pb-4 border-b border-walnut-800/10">Pembayaran</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div @click="paymentMethod = 'va'" 
                             :class="paymentMethod === 'va' ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20'"
                             class="p-5 border flex items-center justify-between cursor-pointer hover:bg-cream-50 transition duration-300">
                            <div class="flex items-center gap-3">
                                <span class="font-bold uppercase tracking-widest text-walnut-950 text-[0.75rem]">Transfer Bank (VA)</span>
                            </div>
                            <div :class="paymentMethod === 'va' ? 'border-gold-500 bg-gold-500' : 'border-walnut-800/30 bg-transparent'" class="w-4 h-4 rounded-full border flex items-center justify-center transition">
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            </div>
                        </div>

                        <div @click="paymentMethod = 'qris'" 
                             :class="paymentMethod === 'qris' ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20'"
                             class="p-5 border flex items-center justify-between cursor-pointer hover:bg-cream-50 transition duration-300">
                            <div class="flex items-center gap-3">
                                <span class="font-bold uppercase tracking-widest text-walnut-950 text-[0.75rem]">QRIS Payment</span>
                            </div>
                            <div :class="paymentMethod === 'qris' ? 'border-gold-500 bg-gold-500' : 'border-walnut-800/30 bg-transparent'" class="w-4 h-4 rounded-full border flex items-center justify-center transition">
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            </div>
                        </div>

                        <div @click="paymentMethod = 'credit_card'" 
                             :class="paymentMethod === 'credit_card' ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20'"
                             class="p-5 border flex items-center justify-between cursor-pointer hover:bg-cream-50 transition duration-300">
                            <div class="flex items-center gap-3">
                                <span class="font-bold uppercase tracking-widest text-walnut-950 text-[0.75rem]">Credit Card</span>
                            </div>
                            <div :class="paymentMethod === 'credit_card' ? 'border-gold-500 bg-gold-500' : 'border-walnut-800/30 bg-transparent'" class="w-4 h-4 rounded-full border flex items-center justify-center transition">
                                <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Order Summary & Coupon -->
            <div class="space-y-6 lg:sticky lg:top-32">
                <div class="bg-cream-50 border border-walnut-800/10 p-8 space-y-8">
                    <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950 pb-6 border-b border-walnut-800/10">Ringkasan</h3>
                    
                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between items-start gap-4 pb-4 border-b border-walnut-800/10 last:border-0 font-medium text-[0.75rem]">
                                <div class="min-w-0">
                                    <span class="text-walnut-950 block uppercase tracking-widest">{{ $item->product->name }}</span>
                                    <span class="text-[0.65rem] text-muted block mt-1">QTY: {{ $item->quantity }}</span>
                                </div>
                                <span class="text-walnut-950 font-bold flex-shrink-0">IDR {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Coupon Input -->
                    <div class="pt-6 border-t border-walnut-800/10 space-y-4">
                        <span class="text-[0.65rem] uppercase tracking-widest text-walnut-800 font-bold block">Privilege Code</span>
                        <p class="text-[0.65rem] text-muted font-bold -mt-2">Gunakan kode <span class="text-gold-600">DEMODJUDAS</span> untuk demo diskon 20%</p>
                        <div class="flex gap-2">
                            <input type="text" x-model="couponCode" placeholder="Kode" class="flex-1 bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium uppercase" />
                            <button type="button" @click="applyCoupon()" class="px-4 bg-walnut-900 text-gold-500 text-[0.6rem] font-bold uppercase tracking-widest hover:bg-gold-600 hover:text-white transition">
                                Apply
                            </button>
                        </div>
                    </div>

                    <!-- Totals Breakdown -->
                    <div class="pt-6 border-t border-walnut-800/10 space-y-4 text-[0.7rem] uppercase tracking-widest font-bold text-muted">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="text-walnut-950">IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Logistik</span>
                            <span class="text-walnut-950" x-show="!shippingLoading">
                                IDR <span x-text="new Intl.NumberFormat('id-ID').format(shippingCost)"></span>
                            </span>
                            <span class="text-gold-500" x-show="shippingLoading" style="display: none;">
                                Menghitung...
                            </span>
                        </div>
                        <div class="flex justify-between" x-show="packingCost > 0" style="display: none;">
                            <span>Proteksi Ekstra</span>
                            <span class="text-walnut-950" x-text="'IDR ' + packingCost.toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex justify-between text-gold-600" x-show="discount > 0" style="display: none;">
                            <span>Privilege Diskon</span>
                            <span x-text="'- IDR ' + discount.toLocaleString('id-ID')">- IDR 0</span>
                        </div>
                        
                        <div class="pt-6 border-t border-walnut-800/10 flex justify-between items-end">
                            <span class="text-walnut-950">Total Akhir</span>
                            <span class="text-3xl font-display font-black text-walnut-950 tracking-tight" x-text="'IDR ' + (subtotal + shippingCost + packingCost - discount).toLocaleString('id-ID')">
                                IDR {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full py-4.5 bg-walnut-900 text-gold-500 font-bold uppercase text-[0.7rem] tracking-[0.2em] hover:bg-gold-600 hover:text-white transition duration-500 mt-6">
                        Selesaikan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
