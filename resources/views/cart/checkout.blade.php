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
              selectedCourier: '',
              selectedService: '',
              paymentMethod: 'va',
              packingOption: 'standard',
              packingCost: 0,
              shippingCost: 0,
              shippingLoading: false,
              courierOptions: [],
              totalWeight: {{ $cartItems->sum(function($item) { return ($item->product->weight ?: 1000) * $item->quantity; }) }},
              selectedCityId: '{{ $defaultAddress ? ($defaultAddress->area_id ?: ($defaultAddress->city_id ?: "")) : "" }}',
              city: '{{ $defaultAddress ? $defaultAddress->city : "" }}',
              province: '{{ $defaultAddress ? $defaultAddress->province : "" }}',
              postalCode: '{{ $defaultAddress ? $defaultAddress->postal_code : "" }}',
              latitude: '{{ $defaultAddress ? $defaultAddress->latitude : "" }}',
              longitude: '{{ $defaultAddress ? $defaultAddress->longitude : "" }}',
              couponCode: '',
              discount: 0,
              subtotal: {{ $subtotal }},
              areaSearchQuery: '',
              areaSearchResults: [],
              selectedNewAreaId: '',
              selectedNewAreaName: '',
              isSearchingArea: false,
              init() {
                  if (this.selectedCityId) {
                      this.fetchCourierOptions();
                  }
              },
              async searchArea() {
                  if (this.areaSearchQuery.length < 3) {
                      this.areaSearchResults = [];
                      return;
                  }
                  this.isSearchingArea = true;
                  try {
                      let res = await fetch('{{ route("api.rajaongkir.search") }}?q=' + encodeURIComponent(this.areaSearchQuery));
                      if (res.ok) {
                          this.areaSearchResults = await res.json();
                      }
                  } catch(e) {}
                  this.isSearchingArea = false;
              },
              selectArea(area) {
                  this.selectedNewAreaId = area.id;
                  this.selectedNewAreaName = area.text;
                  this.areaSearchQuery = area.text;
                  this.postalCode = area.postal_code || '';
                  this.city = area.city || '';
                  this.province = area.province || '';
                  this.latitude = '';
                  this.longitude = '';
                  this.areaSearchResults = [];
                  this.selectedCityId = area.id; // use selectedCityId as area_id for consistency
                  this.fetchCourierOptions();
              },
              async saveNewAddress() {
                  if (!this.selectedNewAreaId || !this.postalCode) {
                      alert('Silakan cari dan pilih kecamatan/kodepos terlebih dahulu dari daftar pencarian.');
                      return;
                  }
                  
                  // Validate required fields
                  let label = document.querySelector('input[name=new_address_label]').value.trim();
                  let name = document.querySelector('input[name=new_address_name]').value.trim();
                  let phone = document.querySelector('input[name=new_address_phone]').value.trim();
                  let address = document.querySelector('textarea[name=new_address_address]').value.trim();
                  
                  if (!label || !name || !phone || !address) {
                      alert('Silakan lengkapi semua field yang diperlukan (Label, Nama, Telepon, Alamat).');
                      return;
                  }
                  
                  let res;
                  try {
                      res = await fetch('{{ route("profile.address.store") }}', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-TOKEN': '{{ csrf_token() }}',
                              'Accept': 'application/json',
                              'X-Requested-With': 'XMLHttpRequest'
                          },
                          body: JSON.stringify({
                              label: label,
                              name: name,
                              phone: phone,
                              address: address,
                              area_id: this.selectedNewAreaId,
                              city: this.city,
                              province: this.province,
                              postal_code: this.postalCode,
                              is_default: 1
                          })
                      });
                      
                      let data = await res.json();
                      
                      if (res.ok && data.success) {
                          alert(data.message || 'Alamat berhasil ditambahkan!');
                          window.location.reload();
                      } else {
                          // Show validation errors or server errors
                          let errorMsg = data.message || '';
                          if (data.errors) {
                              errorMsg = Object.values(data.errors).flat().join('\n');
                          }
                          alert('Gagal menyimpan alamat:\n' + (errorMsg || 'Periksa kembali kelengkapan data'));
                      }
                  } catch (e) {
                      console.error('Save address error:', e);
                      // Try to get text response if possible
                      try {
                          let text = res ? await res.text() : 'No response from server';
                          alert('Error Server:\n' + text.substring(0, 150));
                      } catch (err) {
                          alert('Terjadi kesalahan koneksi. (JS Error: ' + e.message + ')');
                      }
                  }
              },
              async fetchCourierOptions() {
                  if (!this.selectedCityId) return;
                  this.shippingLoading = true;
                  this.courierOptions = [];
                  this.selectedCourier = '';
                  this.selectedService = '';
                  this.shippingCost = 0;
                  
                  try {
                      let res = await fetch('{{ route("api.rajaongkir.rates") }}', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-TOKEN': '{{ csrf_token() }}'
                          },
                          body: JSON.stringify({
                              destination_area_id: this.selectedCityId,
                              postal_code: this.postalCode,
                              weight: this.totalWeight,
                              city: this.city,
                              province: this.province,
                              latitude: this.latitude,
                              longitude: this.longitude
                          })
                      });
                      if (res.ok) {
                          let data = await res.json();
                          if (data.costs) {
                              data.costs.forEach(c => {
                                  this.courierOptions.push({
                                      courier: c.service.split(' - ')[0] || 'COURIER',
                                      courier_name: c.service.split(' - ')[0] || 'COURIER',
                                      service: c.service.split(' - ')[1] || c.description,
                                      description: c.description,
                                      cost: c.cost,
                                      etd: c.etd,
                                      distance: data.distance || null
                                  });
                              });
                          }
                      }
                  } catch(e) { console.error(e); }
                  this.shippingLoading = false;
              },
              selectCourier(option) {
                  this.selectedCourier = option.courier;
                  this.selectedService = option.service;
                  this.shippingCost = option.cost;
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
        
        @if(isset($isBuyNow) && $isBuyNow)
            <input type="hidden" name="is_buy_now" value="1" />
        @endif
        
        <input type="hidden" name="address_id" :value="addressId" />
        <input type="hidden" name="courier" :value="selectedCourier" />
        <input type="hidden" name="courier_service" :value="selectedService" />
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
                            </div>
                            <textarea name="new_address_address" rows="2" placeholder="Detail Alamat" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium"></textarea>
                            <div class="relative w-full">
                                <input type="hidden" name="new_address_area_id" x-model="selectedNewAreaId" />
                                <input type="hidden" name="new_address_area_name" x-model="selectedNewAreaName" />
                                <input type="hidden" name="new_address_city" x-model="city" />
                                <input type="hidden" name="new_address_province" x-model="province" />
                                
                                <input type="text" x-model="areaSearchQuery" @input.debounce.500ms="searchArea()" placeholder="Cari Kecamatan / Kota (Otomatis dari Biteship)" 
                                    class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" autocomplete="off" />
                                
                                <div x-show="isSearchingArea" class="absolute right-0 top-3 text-[0.65rem] text-muted">Mencari...</div>

                                <div x-show="areaSearchResults.length > 0" @click.away="areaSearchResults = []" class="absolute z-10 w-full mt-1 bg-white border border-walnut-800/10 shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="res in areaSearchResults" :key="res.id">
                                        <div @click="selectArea(res)" x-text="res.text" class="p-3 hover:bg-cream-50 cursor-pointer text-[0.75rem] border-b border-walnut-800/5 transition"></div>
                                    </template>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" name="new_address_phone" value="{{ auth()->user()->phone }}" placeholder="No. Telepon" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                <input type="text" name="new_address_postal_code" x-model="postalCode" placeholder="Kode Pos (Otomatis Terisi)" readonly class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium opacity-70 cursor-not-allowed" />
                            </div>
                            <div class="pt-4">
                                <button type="button" @click="saveNewAddress()" class="px-6 py-3 bg-walnut-900 text-gold-500 font-bold uppercase text-[0.65rem] tracking-widest hover:bg-gold-600 hover:text-white transition duration-300">
                                    Simpan Alamat
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($addresses as $addr)
                                <div @click="addressId = '{{ $addr->id }}'; selectedCityId = '{{ $addr->area_id ?: $addr->city_id }}'; postalCode = '{{ $addr->postal_code }}'; city = '{{ $addr->city }}'; province = '{{ $addr->province }}'; latitude = '{{ $addr->latitude }}'; longitude = '{{ $addr->longitude }}'; fetchCourierOptions()" 
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
                                        </div>
                                        <textarea name="new_address_address" rows="2" placeholder="Detail Alamat" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium"></textarea>
                                        <div class="relative w-full">
                                            <input type="hidden" name="new_address_area_id" x-model="selectedNewAreaId" />
                                            <input type="hidden" name="new_address_area_name" x-model="selectedNewAreaName" />
                                            <input type="hidden" name="new_address_city" x-model="city" />
                                            <input type="hidden" name="new_address_province" x-model="province" />
                                            
                                            <input type="text" x-model="areaSearchQuery" @input.debounce.500ms="searchArea()" placeholder="Cari Kecamatan / Kota (Otomatis dari Biteship)" 
                                                class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" autocomplete="off" />
                                            
                                            <div x-show="isSearchingArea" class="absolute right-0 top-3 text-[0.65rem] text-muted">Mencari...</div>

                                            <div x-show="areaSearchResults.length > 0" @click.away="areaSearchResults = []" class="absolute z-10 w-full mt-1 bg-white border border-walnut-800/10 shadow-lg max-h-60 overflow-y-auto">
                                                <template x-for="res in areaSearchResults" :key="res.id">
                                                    <div @click="selectArea(res)" x-text="res.text" class="p-3 hover:bg-cream-50 cursor-pointer text-[0.75rem] border-b border-walnut-800/5 transition"></div>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <input type="text" name="new_address_phone" placeholder="No. Telepon" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                                            <input type="text" name="new_address_postal_code" x-model="postalCode" placeholder="Kode Pos (Otomatis Terisi)" readonly class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium opacity-70 cursor-not-allowed" />
                                        </div>
                                        <div class="pt-4">
                                            <button type="button" @click="saveNewAddress()" class="px-6 py-3 bg-walnut-900 text-gold-500 font-bold uppercase text-[0.65rem] tracking-widest hover:bg-gold-600 hover:text-white transition duration-300">
                                                Simpan Alamat
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Courier Section -->
                <div class="space-y-6 pt-6 border-t border-walnut-800/10">
                    <div class="flex items-center justify-between pb-4 border-b border-walnut-800/10">
                        <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950">Layanan Logistik</h3>
                        <span class="text-[0.65rem] text-muted font-bold uppercase tracking-widest" x-show="totalWeight > 0">
                            Berat: <span x-text="totalWeight >= 1000 ? (totalWeight/1000).toFixed(1) + ' kg' : totalWeight + ' g'"></span>
                        </span>
                    </div>
                    
                    <!-- Loading State -->
                    <div x-show="shippingLoading" class="space-y-3">
                        <template x-for="i in 3">
                            <div class="p-5 border border-walnut-800/10 animate-pulse">
                                <div class="flex justify-between items-center">
                                    <div class="space-y-2">
                                        <div class="h-3 w-24 bg-walnut-800/10"></div>
                                        <div class="h-2 w-32 bg-walnut-800/5"></div>
                                    </div>
                                    <div class="h-4 w-20 bg-walnut-800/10"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- No Address Selected -->
                    <div x-show="!selectedCityId && !shippingLoading" class="p-6 border border-dashed border-walnut-800/20 text-center">
                        <p class="text-[0.75rem] text-muted font-medium">Pilih alamat pengiriman terlebih dahulu untuk melihat opsi kurir.</p>
                    </div>

                    <!-- Courier Options -->
                    <div x-show="courierOptions.length > 0 && !shippingLoading" class="space-y-3">
                        <template x-for="(option, idx) in courierOptions" :key="idx">
                            <div @click="selectCourier(option)"
                                 :class="selectedCourier === option.courier && selectedService === option.service ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20 hover:border-walnut-800/40'"
                                 class="p-5 border cursor-pointer transition duration-300">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div :class="selectedCourier === option.courier && selectedService === option.service ? 'border-gold-500 bg-gold-500' : 'border-walnut-800/30 bg-transparent'"
                                             class="w-4 h-4 rounded-full border flex items-center justify-center transition flex-shrink-0">
                                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[0.75rem] font-bold uppercase tracking-widest text-walnut-950" x-text="option.courier"></span>
                                                <span class="text-[0.6rem] bg-walnut-900 text-gold-500 px-2 py-0.5 font-bold uppercase tracking-wider" x-text="option.service"></span>
                                            </div>
                                            <span class="text-[0.65rem] text-muted font-medium block mt-1" x-text="option.description"></span>
                                            <span class="text-[0.6rem] text-gold-600 font-bold mt-0.5 block">
                                                Estimasi: <span x-text="option.etd"></span> hari
                                                <template x-if="option.distance">
                                                    <span> | Jarak: <span x-text="option.distance"></span> km</span>
                                                </template>
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-[0.8rem] font-black text-walnut-950 tracking-wide flex-shrink-0" x-text="'IDR ' + option.cost.toLocaleString('id-ID')"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- No Results -->
                    <div x-show="selectedCityId && courierOptions.length === 0 && !shippingLoading" class="p-6 border border-dashed border-red-600/30 text-center">
                        <p class="text-[0.75rem] text-red-600 font-bold">Tidak ada layanan kurir tersedia untuk tujuan ini.</p>
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
                    <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950 pb-6 border-b border-walnut-800/10">
                        Ringkasan
                        @if(isset($isBuyNow) && $isBuyNow)
                            <div class="mt-2 flex items-center justify-between bg-gold-500/10 border border-gold-500/30 p-2 text-[0.6rem] tracking-widest font-bold text-gold-600">
                                <span>PEMBELIAN LANGSUNG</span>
                                <a href="{{ route('checkout.cancelBuyNow') }}" class="hover:text-red-600 transition">BATAL</a>
                            </div>
                        @endif
                    </h3>
                    
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
                            <span>Logistik <span x-show="selectedCourier" class="text-gold-600" x-text="'(' + selectedCourier + ' ' + selectedService + ')'"></span></span>
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
