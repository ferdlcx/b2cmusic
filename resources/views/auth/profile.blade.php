@extends('layouts.app')

@section('title', 'Profil Saya - MusicStore Luxe')

@section('content')
<!-- Leaflet Map CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="max-w-[1200px] mx-auto px-4 md:px-8 py-8" 
     x-data="{ 
        activeTab: 'profile',
        showAddAddressModal: false,
        showEditAddressModal: false,
        provinces: [],
        cities: [],
        addMap: null,
        addMarker: null,
        editMap: null,
        editMarker: null,
        editAddressData: {
            id: '',
            label: '',
            name: '',
            phone: '',
            address: '',
            province_id: '',
            province: '',
            city_id: '',
            city: '',
            district: '',
            village: '',
            postal_code: '',
            latitude: '',
            longitude: '',
            is_default: false
        },
        async init() {
            // Load provinces list
            try {
                let res = await fetch('{{ route('api.provinces') }}');
                if (res.ok) {
                    this.provinces = await res.json();
                } else {
                    alert('Gagal memuat data provinsi dari RajaOngkir. Cek koneksi atau konfigurasi API Key.');
                }
            } catch (err) {
                console.error('Error fetching provinces:', err);
                alert('Gagal memuat data provinsi dari RajaOngkir. (Network Error)');
            }
        },
        async handleProvinceChange(e, type) {
            let pId = e.target.value;
            let provinceObj = this.provinces.find(p => p.province_id == pId);
            let pName = provinceObj ? provinceObj.province : '';
            
            if (type === 'add') {
                // Done automatically via form inputs or bindings
            } else {
                this.editAddressData.province = pName;
                this.editAddressData.city_id = '';
                this.editAddressData.city = '';
            }
            
            // Load cities
            this.cities = [];
            if (pId) {
                try {
                    let res = await fetch(`{{ route('api.cities') }}?province_id=${pId}`);
                    if (res.ok) {
                        this.cities = await res.json();
                    }
                } catch (err) {
                    console.error('Error fetching cities:', err);
                }
            }
        },
        handleCityChange(e, type) {
            let cId = e.target.value;
            let cityObj = this.cities.find(c => c.city_id == cId);
            let cityName = cityObj ? `${cityObj.type} ${cityObj.city_name}` : '';
            if (type === 'edit') {
                this.editAddressData.city = cityName;
            }
        },
        initMap(type) {
            this.$nextTick(() => {
                const defaultLat = -6.200000;
                const defaultLng = 106.816666;
                if (type === 'add') {
                    let lat = defaultLat;
                    let lng = defaultLng;
                    
                    if (this.addMap) {
                        this.addMap.invalidateSize();
                        return;
                    }
                    
                    this.addMap = L.map('map-add').setView([lat, lng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(this.addMap);
                    
                    this.addMarker = L.marker([lat, lng], { draggable: true }).addTo(this.addMap);
                    
                    this.addMarker.on('dragend', (e) => {
                        let position = this.addMarker.getLatLng();
                        document.getElementById('add-latitude').value = position.lat.toFixed(6);
                        document.getElementById('add-longitude').value = position.lng.toFixed(6);
                    });
                    
                    this.addMap.on('click', (e) => {
                        this.addMarker.setLatLng(e.latlng);
                        document.getElementById('add-latitude').value = e.latlng.lat.toFixed(6);
                        document.getElementById('add-longitude').value = e.latlng.lng.toFixed(6);
                    });
                    
                    document.getElementById('add-latitude').value = lat.toFixed(6);
                    document.getElementById('add-longitude').value = lng.toFixed(6);
                } else if (type === 'edit') {
                    let lat = parseFloat(this.editAddressData.latitude) || defaultLat;
                    let lng = parseFloat(this.editAddressData.longitude) || defaultLng;
                    
                    if (this.editMap) {
                        this.editMap.setView([lat, lng], 13);
                        this.editMarker.setLatLng([lat, lng]);
                        this.editMap.invalidateSize();
                        return;
                    }
                    
                    this.editMap = L.map('map-edit').setView([lat, lng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(this.editMap);
                    
                    this.editMarker = L.marker([lat, lng], { draggable: true }).addTo(this.editMap);
                    
                    this.editMarker.on('dragend', (e) => {
                        let position = this.editMarker.getLatLng();
                        this.editAddressData.latitude = position.lat.toFixed(6);
                        this.editAddressData.longitude = position.lng.toFixed(6);
                    });
                    
                    this.editMap.on('click', (e) => {
                        this.editMarker.setLatLng(e.latlng);
                        this.editAddressData.latitude = e.latlng.lat.toFixed(6);
                        this.editAddressData.longitude = e.latlng.lng.toFixed(6);
                    });
                }
            });
        },
        async openEditAddress(addr) {
            this.editAddressData = {
                id: addr.id,
                label: addr.label,
                name: addr.name,
                phone: addr.phone,
                address: addr.address,
                province_id: addr.province_id || '',
                province: addr.province || '',
                city_id: addr.city_id || '',
                city: addr.city || '',
                district: addr.district || '',
                village: addr.village || '',
                postal_code: addr.postal_code || '',
                latitude: addr.latitude || '',
                longitude: addr.longitude || '',
                is_default: addr.is_default
            };
            
            // Show modal immediately so it doesn't feel slow
            this.showEditAddressModal = true;
            this.initMap('edit');
            
            // Load cities for the current province of address
            if (addr.province_id) {
                try {
                    let res = await fetch(`{{ route('api.cities') }}?province_id=${addr.province_id}`);
                    if (res.ok) {
                        this.cities = await res.json();
                    }
                } catch (err) {
                    console.error('Error fetching cities for edit:', err);
                }
            }
        }
     }">
     
    <!-- Header -->
    <div class="border-b border-slate-200/60 pb-6 mb-8 space-y-2">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-indigo-600 font-bold block">Manajemen Akun</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tight text-slate-950">Profil Saya</h1>
    </div>

    <div class="grid gap-8 lg:grid-cols-[280px_1fr]">
        <!-- Left Side: Nav Menu -->
        <div class="space-y-2">
            <button @click="activeTab = 'profile'" 
                    :class="activeTab === 'profile' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-100'"
                    class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl text-sm font-semibold tracking-wide transition duration-300">
                <i data-lucide="user" class="w-4.5 h-4.5"></i>
                Detail Profil
            </button>
            <button @click="activeTab = 'addresses'" 
                    :class="activeTab === 'addresses' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-100'"
                    class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl text-sm font-semibold tracking-wide transition duration-300">
                <i data-lucide="map-pin" class="w-4.5 h-4.5"></i>
                Daftar Alamat
            </button>
            <button @click="activeTab = 'password'" 
                    :class="activeTab === 'password' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-100'"
                    class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl text-sm font-semibold tracking-wide transition duration-300">
                <i data-lucide="lock" class="w-4.5 h-4.5"></i>
                Keamanan & Sandi
            </button>
            <a href="{{ route('orders.history') }}" 
               class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl text-sm font-semibold tracking-wide bg-white text-slate-700 hover:bg-slate-50 border border-slate-100 transition duration-300">
                <i data-lucide="package" class="w-4.5 h-4.5"></i>
                Riwayat Pesanan
            </a>
        </div>

        <!-- Right Side: Content Area -->
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 md:p-8 shadow-sm">
            
            <!-- Tab Content: Detail Profil -->
            <div x-show="activeTab === 'profile'" class="space-y-6">
                <div>
                    <h3 class="font-display text-xl font-bold text-slate-950 uppercase tracking-tight">Detail Profil</h3>
                    <p class="text-xs text-slate-500">Perbarui data nama dan nomor telepon Anda.</p>
                </div>
                
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6 max-w-lg">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4.5 py-3 bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 rounded-xl text-sm transition outline-none" />
                        @error('name')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Alamat Email (Tidak dapat diubah)</label>
                        <input type="email" value="{{ $user->email }}" disabled
                               class="w-full px-4.5 py-3 bg-slate-100 border border-slate-200 text-slate-400 rounded-xl text-sm outline-none cursor-not-allowed" />
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">No. Telepon / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4.5 py-3 bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 rounded-xl text-sm transition outline-none" />
                        @error('phone')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold uppercase tracking-wider transition duration-300">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab Content: Keamanan & Sandi -->
            <div x-show="activeTab === 'password'" class="space-y-6" style="display: none;">
                <div>
                    <h3 class="font-display text-xl font-bold text-slate-950 uppercase tracking-tight">Ubah Password</h3>
                    <p class="text-xs text-slate-500">Amankan akun Anda dengan mengganti sandi lama secara berkala.</p>
                </div>
                
                <form action="{{ route('profile.password') }}" method="POST" class="space-y-6 max-w-lg">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Password Saat Ini</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4.5 py-3 bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 rounded-xl text-sm transition outline-none" />
                        @error('current_password')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Password Baru</label>
                        <input type="password" name="password" required
                               class="w-full px-4.5 py-3 bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 rounded-xl text-sm transition outline-none" />
                        @error('password')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Ulangi Password Baru</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4.5 py-3 bg-slate-50 border border-slate-200 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 rounded-xl text-sm transition outline-none" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-bold uppercase tracking-wider transition duration-300">
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab Content: Daftar Alamat -->
            <div x-show="activeTab === 'addresses'" class="space-y-6" style="display: none;">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-display text-xl font-bold text-slate-950 uppercase tracking-tight">Daftar Alamat Pengiriman</h3>
                        <p class="text-xs text-slate-500">Kelola beberapa alamat tujuan pengiriman barang Anda.</p>
                    </div>
                    <button @click="showAddAddressModal = true; initMap('add')" 
                            class="inline-flex items-center gap-1.5 px-4.5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition duration-300">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        Tambah
                    </button>
                </div>

                @if($addresses->isEmpty())
                    <div class="text-center py-12 bg-slate-50/50 rounded-[24px] border border-dashed border-slate-200">
                        <i data-lucide="map" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                        <p class="text-xs font-bold text-slate-600">Belum ada alamat tersimpan</p>
                        <p class="text-[0.7rem] text-slate-400 mt-1">Silakan tambahkan alamat pengiriman pertama Anda untuk mempermudah checkout.</p>
                    </div>
                @else
                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach($addresses as $addr)
                            <div class="border {{ $addr->is_default ? 'border-indigo-200 bg-indigo-50/10' : 'border-slate-200/80 bg-white' }} rounded-2xl p-5 flex flex-col justify-between hover:shadow-md hover:border-slate-300/80 transition duration-300 relative">
                                @if($addr->is_default)
                                    <span class="absolute top-4 right-4 bg-indigo-600 text-white text-[0.55rem] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">Utama</span>
                                @endif
                                
                                <div class="space-y-2">
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="tag" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span class="text-xs font-bold text-slate-900">{{ $addr->label }}</span>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700">{{ $addr->name }}</p>
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        {{ $addr->address }}, 
                                        @if($addr->village) Kel. {{ $addr->village }}, @endif
                                        @if($addr->district) Kec. {{ $addr->district }}, @endif
                                        {{ $addr->city }}, {{ $addr->province }}, {{ $addr->postal_code }}
                                    </p>
                                    <p class="text-xs text-slate-600 font-medium">Telp: {{ $addr->phone }}</p>
                                    @if($addr->latitude && $addr->longitude)
                                        <div class="flex items-center gap-1 text-[0.65rem] text-slate-400 pt-1">
                                            <i data-lucide="map" class="w-3 h-3 text-indigo-500"></i>
                                            <span>{{ $addr->latitude }}, {{ $addr->longitude }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 pt-5 border-t border-slate-100/80 mt-4">
                                    <button @click="openEditAddress({{ json_encode($addr) }})" 
                                            class="text-xs text-indigo-600 hover:text-indigo-700 font-bold transition flex items-center gap-1">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit
                                    </button>
                                    
                                    @if(!$addr->is_default)
                                        <form action="{{ route('profile.address.default', $addr->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs text-slate-600 hover:text-slate-900 font-bold transition">
                                                Set Utama
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('profile.address.destroy', $addr->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-bold transition flex items-center gap-1">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Modal: Tambah Alamat Baru -->
    <div x-show="showAddAddressModal" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         style="display: none;"
         x-transition>
        <div class="bg-white rounded-[32px] w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl p-6 md:p-8" @click.away="showAddAddressModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <h3 class="font-display text-lg font-bold text-slate-950 uppercase tracking-tight">Tambah Alamat Baru</h3>
                <button @click="showAddAddressModal = false" class="p-1 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-800 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('profile.address.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Label Alamat</label>
                        <input type="text" name="label" required placeholder="Contoh: Rumah, Kantor" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Nama Penerima</label>
                        <input type="text" name="name" required placeholder="Nama Penerima" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">No. Telepon Penerima</label>
                        <input type="text" name="phone" required placeholder="No. Telepon" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kode Pos</label>
                        <input type="text" name="postal_code" required placeholder="Kode Pos" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Province Select (RajaOngkir) -->
                    <div class="space-y-1" x-data="{
                        pNameInput: ''
                    }">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Provinsi</label>
                        <input type="hidden" name="province" :value="pNameInput" />
                        <select name="province_id" required 
                                @change="handleProvinceChange($event, 'add'); 
                                         let sel = $event.target; 
                                         pNameInput = sel.options[sel.selectedIndex].text" 
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none">
                            <option value="">Pilih Provinsi</option>
                            <template x-for="p in provinces" :key="p.province_id">
                                <option :value="p.province_id" x-text="p.province"></option>
                            </template>
                        </select>
                    </div>

                    <!-- City Select (RajaOngkir) -->
                    <div class="space-y-1" x-data="{
                        cNameInput: ''
                    }">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kota / Kabupaten</label>
                        <input type="hidden" name="city" :value="cNameInput" />
                        <select name="city_id" required 
                                @change="let sel = $event.target; 
                                         cNameInput = sel.options[sel.selectedIndex].text"
                                :disabled="cities.length === 0"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none disabled:opacity-60 disabled:cursor-not-allowed">
                            <option value="">Pilih Kota</option>
                            <template x-for="c in cities" :key="c.city_id">
                                <option :value="c.city_id" x-text="c.type + ' ' + c.city_name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kecamatan</label>
                        <input type="text" name="district" required placeholder="Kecamatan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kelurahan</label>
                        <input type="text" name="village" required placeholder="Kelurahan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Alamat Lengkap</label>
                    <textarea name="address" required rows="3" placeholder="Nama Jalan, Blok, No. Rumah, RT/RW" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Pinpoint Lokasi (Peta)</label>
                    <p class="text-[0.65rem] text-slate-400 pb-1">Geser pin atau klik pada peta untuk menentukan koordinat.</p>
                    <div id="map-add" class="h-48 rounded-xl border border-slate-200" style="position: relative; outline: none; z-index: 10;"></div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Latitude</label>
                        <input type="text" name="latitude" id="add-latitude" readonly class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm outline-none cursor-not-allowed text-slate-500" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Longitude</label>
                        <input type="text" name="longitude" id="add-longitude" readonly class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm outline-none cursor-not-allowed text-slate-500" />
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_default" value="1" id="is_default_add" class="rounded text-indigo-600 focus:ring-indigo-500" />
                    <label for="is_default_add" class="text-xs text-slate-600 select-none">Jadikan sebagai alamat utama</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 mt-6">
                    <button type="button" @click="showAddAddressModal = false" class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-slate-50 transition duration-300">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition duration-300 shadow-md shadow-indigo-600/10">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Alamat -->
    <div x-show="showEditAddressModal" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         style="display: none;"
         x-transition>
        <div class="bg-white rounded-[32px] w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl p-6 md:p-8" @click.away="showEditAddressModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <h3 class="font-display text-lg font-bold text-slate-950 uppercase tracking-tight">Edit Alamat</h3>
                <button @click="showEditAddressModal = false" class="p-1 rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-800 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form :action="`{{ route('profile.address.store') }}/${editAddressData.id}`" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Label Alamat</label>
                        <input type="text" name="label" required x-model="editAddressData.label" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Nama Penerima</label>
                        <input type="text" name="name" required x-model="editAddressData.name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">No. Telepon Penerima</label>
                        <input type="text" name="phone" required x-model="editAddressData.phone" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kode Pos</label>
                        <input type="text" name="postal_code" required x-model="editAddressData.postal_code" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Province Select (RajaOngkir) -->
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Provinsi</label>
                        <input type="hidden" name="province" :value="editAddressData.province" />
                        <select name="province_id" required 
                                x-model="editAddressData.province_id"
                                @change="handleProvinceChange($event, 'edit')"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none">
                            <option value="">Pilih Provinsi</option>
                            <template x-for="p in provinces" :key="p.province_id">
                                <option :value="p.province_id" x-text="p.province" :selected="p.province_id == editAddressData.province_id"></option>
                            </template>
                        </select>
                    </div>

                    <!-- City Select (RajaOngkir) -->
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kota / Kabupaten</label>
                        <input type="hidden" name="city" :value="editAddressData.city" />
                        <select name="city_id" required 
                                x-model="editAddressData.city_id"
                                @change="handleCityChange($event, 'edit')"
                                :disabled="cities.length === 0"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none disabled:opacity-60 disabled:cursor-not-allowed">
                            <option value="">Pilih Kota</option>
                            <template x-for="c in cities" :key="c.city_id">
                                <option :value="c.city_id" x-text="c.type + ' ' + c.city_name" :selected="c.city_id == editAddressData.city_id"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kecamatan</label>
                        <input type="text" name="district" required x-model="editAddressData.district" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Kelurahan</label>
                        <input type="text" name="village" required x-model="editAddressData.village" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none" />
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Alamat Lengkap</label>
                    <textarea name="address" required rows="3" x-model="editAddressData.address" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-100 focus:border-indigo-600 outline-none"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Pinpoint Lokasi (Peta)</label>
                    <p class="text-[0.65rem] text-slate-400 pb-1">Geser pin atau klik pada peta untuk menentukan koordinat.</p>
                    <div id="map-edit" class="h-48 rounded-xl border border-slate-200" style="position: relative; outline: none; z-index: 10;"></div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Latitude</label>
                        <input type="text" name="latitude" id="edit-latitude" x-model="editAddressData.latitude" readonly class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm outline-none cursor-not-allowed text-slate-500" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Longitude</label>
                        <input type="text" name="longitude" id="edit-longitude" x-model="editAddressData.longitude" readonly class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm outline-none cursor-not-allowed text-slate-500" />
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_default" value="1" id="is_default_edit" x-model="editAddressData.is_default" class="rounded text-indigo-600 focus:ring-indigo-500" />
                    <label for="is_default_edit" class="text-xs text-slate-600 select-none">Jadikan sebagai alamat utama</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 mt-6">
                    <button type="button" @click="showEditAddressModal = false" class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-slate-50 transition duration-300">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition duration-300 shadow-md shadow-indigo-600/10">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endsection
