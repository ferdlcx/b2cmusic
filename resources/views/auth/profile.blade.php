@extends('layouts.app')

@section('title', 'Profil Saya - DjudasMS')

@section('content')
<!-- Leaflet Map CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="py-8" 
     x-data="{ 
        activeTab: 'profile',
        showAddAddressModal: false,
        showEditAddressModal: false,
        provinces: [],
        cities: [],
        rajaongkirFailed: false,
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
            try {
                let res = await fetch('{{ route('api.provinces') }}');
                if (res.ok) {
                    let data = await res.json();
                    if (data && data.length > 0) {
                        this.provinces = data;
                        this.rajaongkirFailed = false;
                    } else {
                        this.rajaongkirFailed = true;
                    }
                } else {
                    this.rajaongkirFailed = true;
                }
            } catch (err) {
                this.rajaongkirFailed = true;
            }
        },
        async handleProvinceChange(e, type) {
            let pId = e.target.value;
            let provinceObj = this.provinces.find(p => p.province_id == pId);
            let pName = provinceObj ? provinceObj.province : '';
            
            if (type !== 'add') {
                this.editAddressData.province = pName;
                this.editAddressData.city_id = '';
                this.editAddressData.city = '';
            }
            
            this.cities = [];
            if (pId) {
                try {
                    let res = await fetch(`{{ route('api.cities') }}?province_id=${pId}`);
                    if (res.ok) {
                        this.cities = await res.json();
                    }
                } catch (err) {}
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
                    if (this.addMap) {
                        this.addMap.invalidateSize();
                        return;
                    }
                    this.addMap = L.map('map-add').setView([defaultLat, defaultLng], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.addMap);
                    this.addMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(this.addMap);
                    
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
                    
                    document.getElementById('add-latitude').value = defaultLat.toFixed(6);
                    document.getElementById('add-longitude').value = defaultLng.toFixed(6);
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
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.editMap);
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
            
            this.showEditAddressModal = true;
            this.initMap('edit');
            
            if (addr.province_id) {
                try {
                    let res = await fetch(`{{ route('api.cities') }}?province_id=${addr.province_id}`);
                    if (res.ok) {
                        this.cities = await res.json();
                    }
                } catch (err) {}
            }
        }
     }">
     
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-8 mb-12 space-y-4">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Manajemen Akun</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">Profil <span class="text-gold-500">Saya.</span></h1>
    </div>

    <div class="grid gap-12 lg:grid-cols-[280px_1fr]">
        <!-- Left Side: Nav Menu -->
        <div class="space-y-4">
            <button @click="activeTab = 'profile'" 
                    :class="activeTab === 'profile' ? 'text-gold-600 border-gold-500' : 'text-muted border-transparent hover:text-walnut-950 hover:border-walnut-800/20'"
                    class="w-full flex items-center justify-between py-4 border-b font-bold uppercase text-[0.7rem] tracking-widest transition duration-300">
                Detail Profil <i data-lucide="user" class="w-4 h-4"></i>
            </button>
            <button @click="activeTab = 'addresses'" 
                    :class="activeTab === 'addresses' ? 'text-gold-600 border-gold-500' : 'text-muted border-transparent hover:text-walnut-950 hover:border-walnut-800/20'"
                    class="w-full flex items-center justify-between py-4 border-b font-bold uppercase text-[0.7rem] tracking-widest transition duration-300">
                Daftar Alamat <i data-lucide="map-pin" class="w-4 h-4"></i>
            </button>
            <button @click="activeTab = 'password'" 
                    :class="activeTab === 'password' ? 'text-gold-600 border-gold-500' : 'text-muted border-transparent hover:text-walnut-950 hover:border-walnut-800/20'"
                    class="w-full flex items-center justify-between py-4 border-b font-bold uppercase text-[0.7rem] tracking-widest transition duration-300">
                Keamanan & Sandi <i data-lucide="lock" class="w-4 h-4"></i>
            </button>
            <a href="{{ route('orders.history') }}" 
               class="w-full flex items-center justify-between py-4 border-b border-transparent text-muted hover:text-walnut-950 hover:border-walnut-800/20 font-bold uppercase text-[0.7rem] tracking-widest transition duration-300">
                Riwayat Pesanan <i data-lucide="package" class="w-4 h-4"></i>
            </a>
        </div>

        <!-- Right Side: Content Area -->
        <div class="bg-transparent border-none">
            
            <!-- Tab Content: Detail Profil -->
            <div x-show="activeTab === 'profile'" class="space-y-8" x-transition.opacity>
                <div class="border-b border-walnut-800/10 pb-4">
                    <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950">Detail Profil</h3>
                    <p class="text-[0.8rem] text-muted font-medium mt-1">Perbarui data diri dan nomor kontak Anda.</p>
                </div>
                
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6 max-w-lg">
                    @csrf
                    
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full bg-transparent border-b border-walnut-800/20 py-3 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.8rem] font-medium" />
                        @error('name')
                            <p class="text-red-600 text-[0.7rem] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Alamat Email (Tidak dapat diubah)</label>
                        <input type="email" value="{{ $user->email }}" disabled
                               class="w-full bg-cream-50 border-b border-walnut-800/10 py-3 text-walnut-500 cursor-not-allowed text-[0.8rem] font-medium px-2" />
                    </div>

                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">No. Telepon / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full bg-transparent border-b border-walnut-800/20 py-3 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.8rem] font-medium" />
                        @error('phone')
                            <p class="text-red-600 text-[0.7rem] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-6">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-8 py-4 bg-walnut-900 hover:bg-gold-600 text-gold-500 hover:text-white text-[0.7rem] font-bold uppercase tracking-widest transition duration-500">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab Content: Keamanan & Sandi -->
            <div x-show="activeTab === 'password'" class="space-y-8" style="display: none;" x-transition.opacity>
                <div class="border-b border-walnut-800/10 pb-4">
                    <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950">Ubah Password</h3>
                    <p class="text-[0.8rem] text-muted font-medium mt-1">Amankan akun Anda dengan mengganti sandi lama.</p>
                </div>
                
                <form action="{{ route('profile.password') }}" method="POST" class="space-y-6 max-w-lg">
                    @csrf
                    
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Password Saat Ini</label>
                        <input type="password" name="current_password" required
                               class="w-full bg-transparent border-b border-walnut-800/20 py-3 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.8rem] font-medium" />
                        @error('current_password')
                            <p class="text-red-600 text-[0.7rem] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Password Baru</label>
                        <input type="password" name="password" required
                               class="w-full bg-transparent border-b border-walnut-800/20 py-3 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.8rem] font-medium" />
                        @error('password')
                            <p class="text-red-600 text-[0.7rem] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Ulangi Password Baru</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full bg-transparent border-b border-walnut-800/20 py-3 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.8rem] font-medium" />
                    </div>

                    <div class="pt-6">
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-8 py-4 bg-walnut-900 hover:bg-gold-600 text-gold-500 hover:text-white text-[0.7rem] font-bold uppercase tracking-widest transition duration-500">
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab Content: Daftar Alamat -->
            <div x-show="activeTab === 'addresses'" class="space-y-8" style="display: none;" x-transition.opacity>
                <div class="flex items-center justify-between border-b border-walnut-800/10 pb-4">
                    <div>
                        <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950">Daftar Alamat Pengiriman</h3>
                    </div>
                    <button @click="showAddAddressModal = true; initMap('add')" 
                            class="inline-flex items-center px-4 py-2 bg-transparent border border-walnut-800/20 text-walnut-900 text-[0.65rem] font-bold uppercase tracking-widest hover:border-gold-500 hover:text-gold-600 transition">
                        + Tambah
                    </button>
                </div>

                @if($addresses->isEmpty())
                    <div class="text-center py-16 bg-cream-50 border border-walnut-800/10">
                        <i data-lucide="map" class="w-8 h-8 text-walnut-800/20 mx-auto mb-4"></i>
                        <p class="text-[0.75rem] font-bold uppercase tracking-widest text-walnut-950">Belum ada alamat tersimpan</p>
                        <p class="text-[0.75rem] text-muted font-medium mt-2">Silakan tambahkan alamat pengiriman Anda.</p>
                    </div>
                @else
                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach($addresses as $addr)
                            <div class="border {{ $addr->is_default ? 'border-gold-500 bg-cream-50' : 'border-walnut-800/20 bg-transparent' }} p-6 flex flex-col justify-between hover:border-gold-500 hover:bg-cream-50 transition duration-300 relative">
                                @if($addr->is_default)
                                    <span class="absolute top-4 right-4 bg-walnut-900 text-gold-500 text-[0.55rem] font-bold px-2 py-1 uppercase tracking-widest">Utama</span>
                                @endif
                                
                                <div class="space-y-2 pr-12">
                                    <span class="text-[0.65rem] font-bold uppercase tracking-widest text-muted">{{ $addr->label }}</span>
                                    <p class="text-[0.8rem] font-bold text-walnut-950">{{ $addr->name }}</p>
                                    <p class="text-[0.75rem] text-muted font-medium leading-relaxed">
                                        {{ $addr->address }}, 
                                        @if($addr->village) Kel. {{ $addr->village }}, @endif
                                        @if($addr->district) Kec. {{ $addr->district }}, @endif
                                        {{ $addr->city }}, {{ $addr->province }}, {{ $addr->postal_code }}
                                    </p>
                                    <p class="text-[0.75rem] text-walnut-900 font-bold tracking-widest">TELP: {{ $addr->phone }}</p>
                                </div>

                                <div class="flex items-center gap-4 pt-6 border-t border-walnut-800/10 mt-6">
                                    <button @click="openEditAddress({{ json_encode($addr) }})" 
                                            class="text-[0.65rem] uppercase tracking-widest text-gold-600 hover:text-walnut-950 font-bold transition">
                                        Edit
                                    </button>
                                    
                                    @if(!$addr->is_default)
                                        <form action="{{ route('profile.address.default', $addr->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-[0.65rem] uppercase tracking-widest text-walnut-600 hover:text-walnut-950 font-bold transition">
                                                Set Utama
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('profile.address.destroy', $addr->id) }}" method="POST" class="inline ml-auto" onsubmit="return confirm('Hapus alamat ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[0.65rem] uppercase tracking-widest text-red-600 hover:text-red-800 font-bold transition">
                                                Hapus
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
         class="fixed inset-0 bg-walnut-950/80 flex items-center justify-center p-4 z-50"
         style="display: none;"
         x-transition>
        <div class="bg-cream-50 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-8 md:p-12 border border-walnut-800/10 relative" @click.away="showAddAddressModal = false">
            <button @click="showAddAddressModal = false" class="absolute top-6 right-6 text-walnut-400 hover:text-walnut-950 transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
            
            <div class="pb-6 border-b border-walnut-800/10 mb-8">
                <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950">Alamat Baru</h3>
            </div>

            <form action="{{ route('profile.address.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Label Alamat</label>
                        <input type="text" name="label" required placeholder="Rumah / Kantor" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Nama Penerima</label>
                        <input type="text" name="name" required placeholder="Nama Lengkap" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">No. Telepon</label>
                        <input type="text" name="phone" required placeholder="Contoh: 0812345678" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Kode Pos</label>
                        <input type="text" name="postal_code" required placeholder="12345" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Provinsi</label>
                        <template x-if="!rajaongkirFailed">
                            <div x-data="{ pNameInput: '' }">
                                <input type="hidden" name="province" :value="pNameInput" />
                                <select name="province_id" required 
                                        @change="handleProvinceChange($event, 'add'); pNameInput = $event.target.options[$event.target.selectedIndex].text" 
                                        class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium appearance-none rounded-none">
                                    <option value="">Pilih Provinsi</option>
                                    <template x-for="p in provinces" :key="p.province_id">
                                        <option :value="p.province_id" x-text="p.province"></option>
                                    </template>
                                </select>
                            </div>
                        </template>
                        <template x-if="rajaongkirFailed">
                            <input type="text" name="province" required placeholder="Provinsi" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                        </template>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Kota / Kabupaten</label>
                        <template x-if="!rajaongkirFailed">
                            <div x-data="{ cNameInput: '' }">
                                <input type="hidden" name="city" :value="cNameInput" />
                                <select name="city_id" required 
                                        @change="cNameInput = $event.target.options[$event.target.selectedIndex].text" 
                                        class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium appearance-none rounded-none">
                                    <option value="">Pilih Kota</option>
                                    <template x-for="c in cities" :key="c.city_id">
                                        <option :value="c.city_id" x-text="c.type + ' ' + c.city_name"></option>
                                    </template>
                                </select>
                            </div>
                        </template>
                        <template x-if="rajaongkirFailed">
                            <input type="text" name="city" required placeholder="Kota" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                        </template>
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Kecamatan</label>
                        <input type="text" name="district" placeholder="Kecamatan" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Kelurahan / Desa</label>
                        <input type="text" name="village" placeholder="Kelurahan" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Detail Alamat Lengkap</label>
                    <textarea name="address" required rows="3" placeholder="Nama jalan, nomor bangunan, detail patokan..." class="w-full bg-transparent border border-walnut-800/20 p-4 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium resize-none"></textarea>
                </div>

                <div class="space-y-3">
                    <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Sematkan Lokasi Peta (Opsional)</label>
                    <div id="map-add" class="w-full h-48 border border-walnut-800/20 bg-walnut-100 z-10"></div>
                    <div class="flex gap-4">
                        <input type="text" id="add-latitude" name="latitude" readonly placeholder="Latitude" class="w-1/2 bg-cream-50 border-b border-walnut-800/10 py-2 text-muted text-[0.7rem] cursor-not-allowed" />
                        <input type="text" id="add-longitude" name="longitude" readonly placeholder="Longitude" class="w-1/2 bg-cream-50 border-b border-walnut-800/10 py-2 text-muted text-[0.7rem] cursor-not-allowed" />
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full py-4 bg-walnut-900 hover:bg-gold-600 text-gold-500 hover:text-white text-[0.7rem] font-bold uppercase tracking-widest transition duration-500">
                        Simpan Alamat Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Alamat -->
    <div x-show="showEditAddressModal" 
         class="fixed inset-0 bg-walnut-950/80 flex items-center justify-center p-4 z-50"
         style="display: none;"
         x-transition>
        <div class="bg-cream-50 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-8 md:p-12 border border-walnut-800/10 relative" @click.away="showEditAddressModal = false">
            <button @click="showEditAddressModal = false" class="absolute top-6 right-6 text-walnut-400 hover:text-walnut-950 transition">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
            
            <div class="pb-6 border-b border-walnut-800/10 mb-8">
                <h3 class="font-display text-2xl font-black uppercase tracking-tighter text-walnut-950">Edit Alamat</h3>
            </div>

            <form :action="`{{ url('profile/address') }}/${editAddressData.id}`" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Label Alamat</label>
                        <input type="text" name="label" x-model="editAddressData.label" required class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Nama Penerima</label>
                        <input type="text" name="name" x-model="editAddressData.name" required class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">No. Telepon</label>
                        <input type="text" name="phone" x-model="editAddressData.phone" required class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Kode Pos</label>
                        <input type="text" name="postal_code" x-model="editAddressData.postal_code" required class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Provinsi</label>
                        <template x-if="!rajaongkirFailed">
                            <div>
                                <input type="hidden" name="province" x-model="editAddressData.province" />
                                <select name="province_id" x-model="editAddressData.province_id" required 
                                        @change="handleProvinceChange($event, 'edit')" 
                                        class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium appearance-none rounded-none">
                                    <option value="">Pilih Provinsi</option>
                                    <template x-for="p in provinces" :key="p.province_id">
                                        <option :value="p.province_id" x-text="p.province"></option>
                                    </template>
                                </select>
                            </div>
                        </template>
                        <template x-if="rajaongkirFailed">
                            <input type="text" name="province" x-model="editAddressData.province" required class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                        </template>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Kota / Kabupaten</label>
                        <template x-if="!rajaongkirFailed">
                            <div>
                                <input type="hidden" name="city" x-model="editAddressData.city" />
                                <select name="city_id" x-model="editAddressData.city_id" required 
                                        @change="handleCityChange($event, 'edit')" 
                                        class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium appearance-none rounded-none">
                                    <option value="">Pilih Kota</option>
                                    <template x-for="c in cities" :key="c.city_id">
                                        <option :value="c.city_id" x-text="c.type + ' ' + c.city_name"></option>
                                    </template>
                                </select>
                            </div>
                        </template>
                        <template x-if="rajaongkirFailed">
                            <input type="text" name="city" x-model="editAddressData.city" required class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                        </template>
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Kecamatan</label>
                        <input type="text" name="district" x-model="editAddressData.district" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                    <div class="space-y-2">
                        <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Kelurahan / Desa</label>
                        <input type="text" name="village" x-model="editAddressData.village" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium" />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Detail Alamat Lengkap</label>
                    <textarea name="address" x-model="editAddressData.address" required rows="3" class="w-full bg-transparent border border-walnut-800/20 p-4 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.75rem] font-medium resize-none"></textarea>
                </div>

                <div class="space-y-3">
                    <label class="text-[0.65rem] uppercase tracking-widest text-muted font-bold block">Perbarui Lokasi Peta</label>
                    <div id="map-edit" class="w-full h-48 border border-walnut-800/20 bg-walnut-100 z-10"></div>
                    <div class="flex gap-4">
                        <input type="text" name="latitude" x-model="editAddressData.latitude" readonly class="w-1/2 bg-cream-50 border-b border-walnut-800/10 py-2 text-muted text-[0.7rem] cursor-not-allowed" />
                        <input type="text" name="longitude" x-model="editAddressData.longitude" readonly class="w-1/2 bg-cream-50 border-b border-walnut-800/10 py-2 text-muted text-[0.7rem] cursor-not-allowed" />
                    </div>
                </div>

                <div class="pt-6 flex justify-end">
                    <button type="submit" class="w-full py-4 bg-walnut-900 hover:bg-gold-600 text-gold-500 hover:text-white text-[0.7rem] font-bold uppercase tracking-widest transition duration-500">
                        Perbarui Alamat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
