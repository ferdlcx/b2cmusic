@extends('layouts.app')

@section('title', 'Dokumentasi & Pengujian - DjudasMS')

@section('content')
<div x-data="{ activeTab: 'overview', apiLoading: false, apiResults: null }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">

    {{-- ═══════════════════════════════════════════ --}}
    {{-- HERO HEADER --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="text-center space-y-4 pb-10 border-b border-walnut-800/10">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Dokumentasi Sistem</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">
            Doc<span class="text-gold-500">Test.</span>
        </h1>
        <p class="text-muted text-sm font-medium max-w-2xl mx-auto leading-relaxed">
            Dokumentasi lengkap fitur, arsitektur, API, dan panduan pengujian sistem e-commerce <strong class="text-walnut-900">DjudasMS</strong> — 
            platform B2C untuk toko alat musik premium.
        </p>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB NAVIGATION --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="flex flex-wrap gap-2 justify-center">
        @php
            $tabs = [
                'overview'     => ['icon' => 'book-open',      'label' => 'Overview'],
                'architecture' => ['icon' => 'layers',         'label' => 'Arsitektur'],
                'api'          => ['icon' => 'globe',          'label' => 'Cek API'],
                'routes'       => ['icon' => 'map',            'label' => 'Routes'],
                'database'     => ['icon' => 'database',       'label' => 'Database'],
                'walkthrough'  => ['icon' => 'footprints',     'label' => 'Walkthrough'],
                'testing'      => ['icon' => 'flask-conical',  'label' => 'Pengujian'],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
            <button @click="activeTab = '{{ $key }}'"
                :class="activeTab === '{{ $key }}'
                    ? 'bg-walnut-900 text-gold-500 border-walnut-900'
                    : 'bg-cream-50 text-walnut-800 border-walnut-800/10 hover:border-gold-500 hover:text-gold-600'"
                class="inline-flex items-center gap-2 px-4 py-2.5 border text-[0.65rem] font-bold uppercase tracking-widest transition duration-300 cursor-pointer">
                <i data-lucide="{{ $tab['icon'] }}" class="w-3.5 h-3.5"></i>
                {{ $tab['label'] }}
            </button>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB: OVERVIEW --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'overview'" x-transition class="space-y-10">
        {{-- Info Proyek --}}
        <div class="bg-cream-50 border border-walnut-800/10 rounded-3xl p-8 md:p-12 space-y-8">
            <div class="flex items-start gap-5">
                <div class="w-14 h-14 bg-walnut-900 text-gold-500 flex items-center justify-center shrink-0 rounded-2xl">
                    <i data-lucide="music" class="w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="font-display text-2xl font-black uppercase tracking-tight text-walnut-950">DjudasMS</h2>
                    <p class="text-muted text-sm mt-1">Platform E-Commerce B2C untuk Alat Musik Premium</p>
                </div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                <div class="space-y-2 bg-white border border-walnut-800/5 rounded-2xl p-5">
                    <span class="text-[0.6rem] uppercase tracking-[0.3em] text-gold-600 font-bold">Framework</span>
                    <p class="text-walnut-950 font-bold text-sm">Laravel 13 + Tailwind CSS v4</p>
                </div>
                <div class="space-y-2 bg-white border border-walnut-800/5 rounded-2xl p-5">
                    <span class="text-[0.6rem] uppercase tracking-[0.3em] text-gold-600 font-bold">Database</span>
                    <p class="text-walnut-950 font-bold text-sm">MySQL (Aiven Cloud)</p>
                </div>
                <div class="space-y-2 bg-white border border-walnut-800/5 rounded-2xl p-5">
                    <span class="text-[0.6rem] uppercase tracking-[0.3em] text-gold-600 font-bold">Deployment</span>
                    <p class="text-walnut-950 font-bold text-sm">Render.com (Web Service)</p>
                </div>
            </div>
        </div>

        {{-- Fitur Utama --}}
        <div class="space-y-6">
            <h3 class="font-display text-xl font-black uppercase tracking-tight text-walnut-950 border-b border-walnut-800/10 pb-3">Fitur Utama Sistem</h3>
            <div class="grid md:grid-cols-2 gap-4">
                @php
                    $features = [
                        ['icon' => 'shopping-bag',   'title' => 'Katalog & Pencarian',    'desc' => 'Pencarian produk, filter kategori/brand, detail produk dengan spesifikasi & galeri gambar.'],
                        ['icon' => 'shopping-cart',   'title' => 'Keranjang & Checkout',   'desc' => 'Cart management, kupon diskon, kalkulasi ongkir (RajaOngkir), pembayaran Midtrans.'],
                        ['icon' => 'truck',           'title' => 'Tracking Pengiriman',    'desc' => 'Integrasi Biteship untuk pelacakan real-time, notifikasi webhook otomatis.'],
                        ['icon' => 'user-check',      'title' => 'Auth & Verifikasi',     'desc' => 'Register, login, email OTP verification, forgot/reset password via MailerSend.'],
                        ['icon' => 'heart',           'title' => 'Wishlist & Review',      'desc' => 'Simpan produk favorit, tulis ulasan + foto, sistem moderasi admin.'],
                        ['icon' => 'refresh-cw',      'title' => 'Retur & Komplain',      'desc' => 'Pengajuan retur barang dengan bukti foto/video, persetujuan admin.'],
                        ['icon' => 'layout-dashboard','title' => 'Admin Dashboard',        'desc' => 'CRUD produk/kategori/brand, manajemen pesanan, cetak resi & label pengiriman.'],
                        ['icon' => 'bar-chart-3',     'title' => 'Laporan & Analitik',     'desc' => 'Laporan penjualan, produk terlaris, pelanggan aktif, export PDF/Excel.'],
                        ['icon' => 'zap',             'title' => 'Flash Sale & Kupon',     'desc' => 'Buat event flash sale terjadwal, kelola kupon diskon fixed/persentase.'],
                        ['icon' => 'shield',          'title' => 'Role-Based Access',      'desc' => 'Tiga level: Customer, Admin, Super Admin dengan fitur terbatas per role.'],
                    ];
                @endphp
                @foreach($features as $f)
                <div class="flex gap-4 bg-cream-50 border border-walnut-800/5 rounded-2xl p-5 hover:border-gold-500/30 transition">
                    <div class="w-10 h-10 bg-gold-500/10 text-gold-600 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="{{ $f['icon'] }}" class="w-4.5 h-4.5"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-widest text-walnut-950">{{ $f['title'] }}</h4>
                        <p class="text-[0.7rem] text-muted mt-1 leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Akun Demo --}}
        <div class="bg-walnut-900 text-cream-50 rounded-3xl p-8 md:p-10 space-y-5">
            <h3 class="font-display text-lg font-black uppercase tracking-widest text-gold-500">Akun Demo Pengujian</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="bg-walnut-950/50 rounded-2xl p-5 space-y-2 border border-cream-50/5">
                    <span class="text-[0.6rem] uppercase tracking-[0.3em] text-gold-400 font-bold block">Customer</span>
                    <p class="text-sm font-bold text-cream-50">user@musicstore.com</p>
                    <p class="text-xs text-cream-50/60">Password: <code class="text-gold-400">password</code></p>
                </div>
                <div class="bg-walnut-950/50 rounded-2xl p-5 space-y-2 border border-cream-50/5">
                    <span class="text-[0.6rem] uppercase tracking-[0.3em] text-gold-400 font-bold block">Admin</span>
                    <p class="text-sm font-bold text-cream-50">admin@musicstore.com</p>
                    <p class="text-xs text-cream-50/60">Password: <code class="text-gold-400">password</code></p>
                </div>
                <div class="bg-walnut-950/50 rounded-2xl p-5 space-y-2 border border-cream-50/5">
                    <span class="text-[0.6rem] uppercase tracking-[0.3em] text-gold-400 font-bold block">Super Admin</span>
                    <p class="text-sm font-bold text-cream-50">admin@musicstore.com</p>
                    <p class="text-xs text-cream-50/60">Password: <code class="text-gold-400">password</code></p>
                    <p class="text-[0.55rem] text-cream-50/40">(Role super_admin memiliki akses penuh)</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB: ARSITEKTUR --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'architecture'" x-transition class="space-y-10">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-walnut-950 border-b border-walnut-800/10 pb-3">Arsitektur Sistem</h2>

        {{-- Tech Stack --}}
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gold-600">Technology Stack</h3>
            <div class="overflow-x-auto bg-cream-50 border border-walnut-800/10 rounded-2xl">
                <table class="w-full text-sm">
                    <thead class="bg-walnut-900 text-gold-500 text-[0.6rem] uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-3 text-left">Layer</th>
                            <th class="px-6 py-3 text-left">Teknologi</th>
                            <th class="px-6 py-3 text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-walnut-800/5 text-walnut-900 font-medium text-xs">
                        <tr><td class="px-6 py-3 font-bold">Backend</td><td class="px-6 py-3">Laravel 13 (PHP 8.3)</td><td class="px-6 py-3">MVC Pattern, Eloquent ORM, Blade Templating</td></tr>
                        <tr><td class="px-6 py-3 font-bold">Frontend</td><td class="px-6 py-3">Tailwind CSS v4, Alpine.js, Lucide Icons</td><td class="px-6 py-3">Responsive, Utility-first CSS</td></tr>
                        <tr><td class="px-6 py-3 font-bold">Build</td><td class="px-6 py-3">Vite 6</td><td class="px-6 py-3">Hot Module Replacement, Asset Bundling</td></tr>
                        <tr><td class="px-6 py-3 font-bold">Database</td><td class="px-6 py-3">MySQL 8 (Aiven Cloud)</td><td class="px-6 py-3">Cloud-managed, SSL enforced</td></tr>
                        <tr><td class="px-6 py-3 font-bold">Payment</td><td class="px-6 py-3">Midtrans (Sandbox)</td><td class="px-6 py-3">Snap.js popup, webhook callback</td></tr>
                        <tr><td class="px-6 py-3 font-bold">Shipping</td><td class="px-6 py-3">RajaOngkir Komerce + Biteship</td><td class="px-6 py-3">Ongkir domestik + tracking real-time</td></tr>
                        <tr><td class="px-6 py-3 font-bold">Email</td><td class="px-6 py-3">MailerSend API</td><td class="px-6 py-3">Transactional emails (OTP, notifikasi)</td></tr>
                        <tr><td class="px-6 py-3 font-bold">Hosting</td><td class="px-6 py-3">Render.com</td><td class="px-6 py-3">Auto-deploy dari GitHub, ephemeral disk</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Diagram Alur --}}
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gold-600">Alur Transaksi Utama</h3>
            <div class="bg-cream-50 border border-walnut-800/10 rounded-2xl p-8 space-y-4">
                @php
                    $flow = [
                        ['step' => '1', 'title' => 'Browse & Pilih Produk',  'desc' => 'Customer melihat katalog, filter, dan detail produk',  'icon' => 'search'],
                        ['step' => '2', 'title' => 'Tambah ke Keranjang',    'desc' => 'Masukkan produk ke cart dengan jumlah yang diinginkan', 'icon' => 'shopping-cart'],
                        ['step' => '3', 'title' => 'Checkout & Ongkir',      'desc' => 'Pilih alamat, hitung ongkir via RajaOngkir, gunakan kupon', 'icon' => 'map-pin'],
                        ['step' => '4', 'title' => 'Pembayaran Midtrans',    'desc' => 'Bayar via Snap popup (VA, e-wallet, CC). Webhook update status', 'icon' => 'credit-card'],
                        ['step' => '5', 'title' => 'Admin Proses & Kirim',   'desc' => 'Admin menerima pesanan, buat resi Biteship, cetak label', 'icon' => 'package'],
                        ['step' => '6', 'title' => 'Tracking & Selesai',     'desc' => 'Customer track real-time. Webhook update status → Completed', 'icon' => 'check-circle'],
                    ];
                @endphp
                <div class="grid md:grid-cols-3 gap-4">
                    @foreach($flow as $f)
                    <div class="flex gap-3 items-start">
                        <div class="w-9 h-9 bg-walnut-900 text-gold-500 rounded-xl flex items-center justify-center shrink-0 text-xs font-black">{{ $f['step'] }}</div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wide text-walnut-950">{{ $f['title'] }}</h4>
                            <p class="text-[0.65rem] text-muted leading-relaxed mt-0.5">{{ $f['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Integrasi API --}}
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gold-600">Integrasi API Eksternal</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-cream-50 border border-walnut-800/10 rounded-2xl p-6 space-y-3">
                    <h4 class="text-sm font-bold text-walnut-950">Midtrans (Payment Gateway)</h4>
                    <ul class="text-[0.7rem] text-muted space-y-1.5 list-disc list-inside">
                        <li>Mode: <strong class="text-walnut-900">Sandbox</strong> (bukan production)</li>
                        <li>Integrasi: Snap.js popup di halaman checkout</li>
                        <li>Webhook: <code class="bg-walnut-900/5 px-1.5 py-0.5 rounded text-walnut-900 text-[0.65rem]">/midtrans/webhook</code></li>
                        <li>Status yang dihandle: <code class="text-[0.65rem]">capture, settlement, expire, cancel, deny</code></li>
                    </ul>
                </div>
                <div class="bg-cream-50 border border-walnut-800/10 rounded-2xl p-6 space-y-3">
                    <h4 class="text-sm font-bold text-walnut-950">RajaOngkir Komerce (Ongkir)</h4>
                    <ul class="text-[0.7rem] text-muted space-y-1.5 list-disc list-inside">
                        <li>Pencarian area domestik (autocomplete)</li>
                        <li>Kalkulasi tarif pengiriman (JNE, J&T, SiCepat, dll.)</li>
                        <li>Endpoint: <code class="bg-walnut-900/5 px-1.5 py-0.5 rounded text-walnut-900 text-[0.65rem]">/api/rajaongkir/search-area</code> & <code class="text-[0.65rem]">/api/rajaongkir/rates</code></li>
                    </ul>
                </div>
                <div class="bg-cream-50 border border-walnut-800/10 rounded-2xl p-6 space-y-3">
                    <h4 class="text-sm font-bold text-walnut-950">Biteship (Tracking Kurir)</h4>
                    <ul class="text-[0.7rem] text-muted space-y-1.5 list-disc list-inside">
                        <li>Tracking nomor resi real-time</li>
                        <li>Webhook update status pengiriman otomatis</li>
                        <li>Webhook: <code class="bg-walnut-900/5 px-1.5 py-0.5 rounded text-walnut-900 text-[0.65rem]">/api/biteship/webhook</code></li>
                        <li>Sandbox simulator di <a href="/simulasi" class="text-gold-600 font-bold hover:underline">/simulasi</a></li>
                    </ul>
                </div>
                <div class="bg-cream-50 border border-walnut-800/10 rounded-2xl p-6 space-y-3">
                    <h4 class="text-sm font-bold text-walnut-950">MailerSend (Email Service)</h4>
                    <ul class="text-[0.7rem] text-muted space-y-1.5 list-disc list-inside">
                        <li>Kirim OTP verifikasi email saat registrasi</li>
                        <li>Kirim link reset password</li>
                        <li>Notifikasi email transaksional (order confirmation)</li>
                        <li>Test: <code class="bg-walnut-900/5 px-1.5 py-0.5 rounded text-walnut-900 text-[0.65rem]">/dmail</code> & <code class="text-[0.65rem]">/sendmail</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB: CEK API --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'api'" x-transition class="space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-walnut-800/10 pb-4">
            <div>
                <h2 class="font-display text-2xl font-black uppercase tracking-tight text-walnut-950">Status API Eksternal</h2>
                <p class="text-xs text-muted mt-1">Cek konektivitas dan limit API pihak ketiga yang digunakan sistem secara real-time.</p>
            </div>
            <button @click="apiLoading = true; fetch('/cekapi').then(r => r.text()).then(html => { 
                        const parser = new DOMParser(); 
                        const doc = parser.parseFromString(html, 'text/html'); 
                        const rows = doc.querySelectorAll('table tr'); 
                        const results = []; 
                        rows.forEach((row, i) => { 
                            if (i === 0) return; 
                            const cells = row.querySelectorAll('td'); 
                            if (cells.length >= 5) results.push({ name: cells[0].textContent.trim(), status: cells[1].textContent.trim(), code: cells[2].textContent.trim(), time: cells[3].textContent.trim(), detail: cells[4].textContent.trim() }); 
                        }); 
                        apiResults = results; 
                        apiLoading = false; 
                    }).catch(() => { apiLoading = false; })"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-walnut-900 text-gold-500 text-[0.65rem] font-bold uppercase tracking-widest hover:bg-gold-600 hover:text-white transition duration-300 cursor-pointer shrink-0"
                :disabled="apiLoading">
                <i data-lucide="activity" class="w-3.5 h-3.5" :class="apiLoading && 'animate-pulse'"></i>
                <span x-text="apiLoading ? 'Memeriksa...' : 'Cek Semua API'"></span>
            </button>
        </div>

        <template x-if="apiResults === null && !apiLoading">
            <div class="text-center py-16 bg-cream-50 border border-walnut-800/5 rounded-3xl">
                <div class="w-16 h-16 bg-walnut-900/5 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="wifi" class="w-7 h-7 text-walnut-400"></i>
                </div>
                <h3 class="font-display text-lg font-bold uppercase tracking-tight text-walnut-950">Belum Diperiksa</h3>
                <p class="text-xs text-muted mt-1">Klik tombol "Cek Semua API" di atas untuk memeriksa status konektivitas.</p>
            </div>
        </template>

        <template x-if="apiLoading">
            <div class="text-center py-16 bg-cream-50 border border-walnut-800/5 rounded-3xl">
                <div class="w-12 h-12 border-4 border-gold-500/30 border-t-gold-500 rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-xs text-muted font-bold uppercase tracking-widest">Menghubungi server API...</p>
            </div>
        </template>

        <template x-if="apiResults !== null && !apiLoading">
            <div class="overflow-x-auto bg-cream-50 border border-walnut-800/10 rounded-2xl">
                <table class="w-full text-sm">
                    <thead class="bg-walnut-900 text-gold-500 text-[0.6rem] uppercase tracking-widest">
                        <tr>
                            <th class="px-6 py-3 text-left">Layanan API</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Code</th>
                            <th class="px-6 py-3 text-left">Response Time</th>
                            <th class="px-6 py-3 text-left">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-walnut-800/5">
                        <template x-for="api in apiResults" :key="api.name">
                            <tr class="text-xs font-medium text-walnut-900">
                                <td class="px-6 py-3 font-bold" x-text="api.name"></td>
                                <td class="px-6 py-3">
                                    <span :class="api.status.includes('OK') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : api.status.includes('SKIP') ? 'bg-cream-200 text-walnut-600 border-walnut-800/10' : 'bg-rose-50 text-rose-700 border-rose-200'"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.6rem] font-bold uppercase border"
                                        x-text="api.status"></span>
                                </td>
                                <td class="px-6 py-3 font-mono text-[0.65rem]" x-text="api.code"></td>
                                <td class="px-6 py-3" x-text="api.time"></td>
                                <td class="px-6 py-3 text-[0.65rem] text-muted max-w-[250px] truncate" x-text="api.detail"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </template>

        <div class="bg-gold-500/5 border border-gold-500/20 rounded-2xl p-5 text-xs text-walnut-800 space-y-2">
            <p class="font-bold text-gold-600 uppercase tracking-widest text-[0.6rem]">ℹ️ Catatan</p>
            <p>Pengecekan ini memanggil endpoint <code class="bg-walnut-900/5 px-1.5 py-0.5 rounded font-mono text-[0.65rem]">/cekapi</code> yang sudah ada di sistem. 
            Jika status menunjukkan <strong>LIMIT / ERROR</strong>, berarti kuota harian API tersebut sudah terpakai atau token perlu diperbarui.</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB: ROUTES --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'routes'" x-transition class="space-y-8">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-walnut-950 border-b border-walnut-800/10 pb-3">Daftar Routes Sistem</h2>

        @php
            $routeGroups = [
                'Public (Tanpa Login)' => [
                    ['GET', '/', 'home', 'Landing page utama'],
                    ['GET', '/catalog', 'catalog', 'Halaman katalog produk'],
                    ['GET', '/product/{slug}', 'products.show', 'Detail produk'],
                    ['GET', '/about', 'about', 'Halaman tentang kami'],
                    ['GET', '/contact', 'contact', 'Halaman kontak'],
                    ['GET', '/cekapi', '-', 'Cek status limit API (GUI)'],
                    ['GET', '/doctest', '-', 'Halaman dokumentasi ini'],
                ],
                'Autentikasi (Guest)' => [
                    ['GET', '/register', 'register', 'Halaman registrasi'],
                    ['POST', '/register', '-', 'Proses registrasi'],
                    ['GET', '/login', 'login', 'Halaman login'],
                    ['POST', '/login', '-', 'Proses login'],
                    ['POST', '/logout', 'logout', 'Proses logout'],
                    ['GET', '/forgot-password', 'password.request', 'Lupa password'],
                    ['POST', '/forgot-password', 'password.email', 'Kirim link reset'],
                    ['GET', '/reset-password', 'password.reset', 'Form reset password'],
                    ['POST', '/reset-password', 'password.update', 'Proses reset password'],
                ],
                'Verifikasi Email (Auth)' => [
                    ['GET', '/email/verify', 'verification.notice', 'Halaman verifikasi OTP'],
                    ['POST', '/email/verify/otp', 'verification.verify.otp', 'Verifikasi kode OTP'],
                    ['POST', '/email/verification-notification', 'verification.send', 'Kirim ulang OTP'],
                ],
                'Customer (Auth + Verified)' => [
                    ['GET', '/dashboard', 'customer.dashboard', 'Dashboard pelanggan'],
                    ['GET', '/cart', 'cart.index', 'Halaman keranjang'],
                    ['POST', '/cart/add', 'cart.add', 'Tambah item ke cart'],
                    ['POST', '/cart/update/{id}', 'cart.update', 'Update jumlah item'],
                    ['DELETE', '/cart/remove/{id}', 'cart.remove', 'Hapus item dari cart'],
                    ['GET', '/wishlist', 'wishlist.index', 'Halaman wishlist'],
                    ['POST', '/wishlist/add', 'wishlist.add', 'Tambah ke wishlist'],
                    ['GET', '/checkout', 'checkout.index', 'Halaman checkout'],
                    ['POST', '/checkout', 'checkout.process', 'Proses checkout'],
                    ['POST', '/checkout/shipping-cost', 'checkout.shippingCost', 'Hitung ongkir (AJAX)'],
                    ['GET', '/orders', 'orders.history', 'Riwayat pesanan'],
                    ['GET', '/order/{code}', 'orders.show', 'Detail pesanan'],
                    ['GET', '/order/{code}/invoice', 'orders.invoice', 'Download invoice PDF'],
                    ['GET', '/order/{id}/track', 'orders.track', 'Halaman tracking'],
                    ['POST', '/review', 'reviews.store', 'Kirim ulasan produk'],
                    ['GET', '/return/{order_id}/create', 'returns.create', 'Form pengajuan retur'],
                    ['GET', '/profile', 'profile.show', 'Halaman profil'],
                    ['GET', '/notifications', 'notifications.index', 'Notifikasi pelanggan'],
                ],
                'Simulator Sandbox (Auth)' => [
                    ['GET', '/simulasi', 'simulasi.index', 'Halaman simulator Biteship'],
                    ['POST', '/simulasi/{id}/ship', 'simulasi.ship', 'Simulasi pick-up kurir'],
                    ['POST', '/simulasi/{id}/arrive', 'simulasi.arrive', 'Simulasi paket tiba'],
                ],
                'Admin (/admin/...)' => [
                    ['GET', '/admin/dashboard', 'admin.dashboard', 'Dashboard admin'],
                    ['GET', '/admin/products', 'admin.products', 'Kelola produk'],
                    ['GET', '/admin/products/create', 'admin.products.create', 'Tambah produk'],
                    ['GET', '/admin/categories', 'admin.categories', 'Kelola kategori'],
                    ['GET', '/admin/brands', 'admin.brands', 'Kelola merek'],
                    ['GET', '/admin/orders', 'admin.orders', 'Kelola pesanan'],
                    ['GET', '/admin/orders/{id}', 'admin.orders.show', 'Detail pesanan admin'],
                    ['POST', '/admin/orders/{id}/ship', 'admin.orders.ship', 'Buat resi & kirim'],
                    ['GET', '/admin/orders/{id}/print-label', 'admin.orders.print_label', 'Cetak label pengiriman'],
                    ['GET', '/admin/reviews', 'admin.reviews', 'Moderasi ulasan'],
                    ['GET', '/admin/returns', 'admin.returns', 'Kelola retur'],
                ],
                'Super Admin Only' => [
                    ['GET', '/admin/users', 'admin.users', 'Kelola pengguna'],
                    ['GET', '/admin/coupons', 'admin.coupons', 'Kelola kupon'],
                    ['GET', '/admin/flash-sales', 'admin.flashSales', 'Kelola flash sale'],
                    ['GET', '/admin/reports/sales', 'admin.reports.sales', 'Laporan penjualan'],
                    ['GET', '/admin/reports/products', 'admin.reports.products', 'Laporan produk'],
                    ['GET', '/admin/reports/customers', 'admin.reports.customers', 'Laporan pelanggan'],
                    ['GET', '/admin/activity-log', 'admin.activityLog', 'Audit log aktivitas'],
                ],
                'Webhook (Eksternal)' => [
                    ['POST', '/midtrans/webhook', 'midtrans.webhook', 'Callback pembayaran Midtrans'],
                    ['ANY', '/api/biteship/webhook', 'biteship.webhook', 'Callback tracking Biteship'],
                ],
            ];
        @endphp

        @foreach($routeGroups as $group => $routes)
        <div class="space-y-3">
            <h3 class="text-[0.7rem] font-bold uppercase tracking-widest text-gold-600">{{ $group }}</h3>
            <div class="overflow-x-auto bg-cream-50 border border-walnut-800/10 rounded-2xl">
                <table class="w-full text-xs">
                    <thead class="bg-walnut-900/5 text-walnut-600 uppercase text-[0.55rem] tracking-widest font-bold">
                        <tr>
                            <th class="px-4 py-2.5 text-left w-16">Method</th>
                            <th class="px-4 py-2.5 text-left">URI</th>
                            <th class="px-4 py-2.5 text-left">Route Name</th>
                            <th class="px-4 py-2.5 text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-walnut-800/5 text-walnut-900 font-medium">
                        @foreach($routes as $r)
                        <tr class="hover:bg-walnut-50/50 transition-colors">
                            <td class="px-4 py-2">
                                <span class="inline-flex px-2 py-0.5 rounded text-[0.55rem] font-black uppercase
                                    {{ $r[0] === 'GET' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                    {{ $r[0] === 'POST' ? 'bg-gold-500/10 text-gold-600 border border-gold-500/20' : '' }}
                                    {{ $r[0] === 'PUT' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                    {{ $r[0] === 'DELETE' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                                    {{ $r[0] === 'ANY' ? 'bg-walnut-900/5 text-walnut-800 border border-walnut-800/10' : '' }}">
                                    {{ $r[0] }}
                                </span>
                            </td>
                            <td class="px-4 py-2 font-mono text-[0.65rem]">{{ $r[1] }}</td>
                            <td class="px-4 py-2 text-muted text-[0.65rem]">{{ $r[2] }}</td>
                            <td class="px-4 py-2 text-[0.65rem]">{{ $r[3] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB: DATABASE --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'database'" x-transition class="space-y-8">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-walnut-950 border-b border-walnut-800/10 pb-3">Skema Database</h2>

        @php
            $tables = [
                ['users', 'Data pengguna (customer, admin, super_admin)', ['id', 'name', 'email', 'password', 'role', 'phone', 'profile_photo', 'email_verified_at', 'otp_code', 'otp_expires_at', 'status']],
                ['addresses', 'Alamat pengiriman pengguna', ['id', 'user_id (FK)', 'label', 'recipient_name', 'phone', 'address', 'city', 'province', 'postal_code', 'area_id', 'is_default']],
                ['categories', 'Kategori produk (mendukung nested)', ['id', 'name', 'slug', 'parent_id (FK self)', 'description', 'image', 'status']],
                ['brands', 'Merek/brand produk', ['id', 'name', 'slug', 'description', 'logo', 'status']],
                ['products', 'Data produk utama', ['id', 'category_id (FK)', 'brand_id (FK)', 'name', 'slug', 'description', 'price', 'stock', 'weight', 'status', 'deleted_at (soft delete)']],
                ['product_images', 'Galeri gambar produk', ['id', 'product_id (FK)', 'image', 'is_primary']],
                ['product_specifications', 'Spesifikasi teknis produk', ['id', 'product_id (FK)', 'spec_name', 'spec_value']],
                ['carts / cart_items', 'Keranjang belanja', ['cart: id, user_id', 'cart_items: cart_id, product_id, quantity']],
                ['wishlists / wishlist_items', 'Daftar keinginan', ['wishlist: id, user_id', 'wishlist_items: wishlist_id, product_id']],
                ['orders', 'Data pesanan', ['id', 'user_id (FK)', 'order_code', 'status', 'subtotal', 'shipping_cost', 'discount', 'total', 'shipping_address (JSON)', 'notes', 'biteship_order_id']],
                ['order_items', 'Item dalam pesanan', ['id', 'order_id (FK)', 'product_id (FK)', 'product_name', 'quantity', 'price']],
                ['payments', 'Data pembayaran Midtrans', ['id', 'order_id (FK)', 'payment_type', 'transaction_id', 'status', 'amount', 'snap_token', 'paid_at']],
                ['shipments', 'Data pengiriman', ['id', 'order_id (FK)', 'courier', 'service', 'tracking_number', 'status', 'shipped_at', 'delivered_at', 'biteship_order_id']],
                ['reviews', 'Ulasan produk oleh customer', ['id', 'user_id (FK)', 'product_id (FK)', 'order_id (FK)', 'rating', 'comment', 'photo', 'status']],
                ['coupons', 'Kupon diskon', ['id', 'code', 'type (fixed/percent)', 'value', 'min_purchase', 'max_discount', 'start_date', 'end_date', 'status']],
                ['flash_sales / flash_sale_items', 'Event flash sale', ['flash_sales: id, name, start_time, end_time, status', 'items: flash_sale_id, product_id, discount_price, stock, sold']],
                ['return_requests', 'Pengajuan retur barang', ['id', 'order_id (FK)', 'user_id (FK)', 'reason', 'photo', 'video', 'status', 'admin_notes']],
                ['activity_logs', 'Audit trail aktivitas admin', ['id', 'user_id (FK)', 'action', 'model_type', 'model_id', 'description']],
                ['notifications', 'Notifikasi database Laravel', ['id', 'type', 'notifiable_type/id', 'data (JSON)', 'read_at']],
            ];
        @endphp

        <div class="space-y-4">
            @foreach($tables as $t)
            <details class="group bg-cream-50 border border-walnut-800/10 rounded-2xl overflow-hidden">
                <summary class="flex items-center justify-between px-6 py-4 cursor-pointer hover:bg-walnut-50/50 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-walnut-900/5 rounded-lg flex items-center justify-center">
                            <i data-lucide="table" class="w-4 h-4 text-gold-600"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-widest text-walnut-950">{{ $t[0] }}</h4>
                            <p class="text-[0.6rem] text-muted">{{ $t[1] }}</p>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-walnut-400 group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="px-6 pb-4">
                    <div class="flex flex-wrap gap-1.5 pt-2 border-t border-walnut-800/5">
                        @foreach($t[2] as $col)
                        <span class="inline-flex px-2 py-0.5 rounded text-[0.6rem] font-mono
                            {{ str_contains($col, 'FK') ? 'bg-gold-500/10 text-gold-600 border border-gold-500/20' : 'bg-walnut-900/5 text-walnut-800 border border-walnut-800/5' }}">
                            {{ $col }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </details>
            @endforeach
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB: WALKTHROUGH --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'walkthrough'" x-transition class="space-y-10">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-walnut-950 border-b border-walnut-800/10 pb-3">Panduan Penggunaan (Walkthrough)</h2>

        @php
            $walkthroughs = [
                [
                    'role' => 'Customer',
                    'color' => 'gold',
                    'icon' => 'user',
                    'steps' => [
                        ['Registrasi & Verifikasi', 'Buka /register → isi data → cek email untuk OTP → verifikasi → akun aktif.'],
                        ['Lengkapi Profil', 'Buka /profile → isi nama, telepon, upload foto → tambahkan alamat pengiriman (cari area via autocomplete RajaOngkir).'],
                        ['Jelajahi Katalog', 'Buka /catalog → gunakan filter kategori/brand/harga → klik produk untuk lihat detail, spesifikasi, dan galeri.'],
                        ['Tambah ke Keranjang', 'Di halaman detail produk, klik "Tambah ke Keranjang" → bisa juga via "Beli Langsung" untuk skip cart.'],
                        ['Wishlist (Opsional)', 'Klik ikon hati di produk → simpan ke wishlist → bisa dipindahkan ke cart nanti.'],
                        ['Checkout & Bayar', 'Buka /cart → klik Checkout → pilih alamat → pilih kurir (ongkir otomatis) → masukkan kupon (opsional) → klik Bayar → popup Midtrans muncul → pilih metode pembayaran.'],
                        ['Lacak Pesanan', 'Buka /orders → klik pesanan → lihat status real-time → klik "Lacak Pengiriman" untuk tracking via Biteship.'],
                        ['Konfirmasi & Review', 'Setelah paket sampai, konfirmasi penerimaan → tulis ulasan + upload foto → kirim.'],
                        ['Ajukan Retur (Jika Perlu)', 'Buka /orders → klik "Ajukan Retur" → pilih item, isi alasan, upload bukti foto/video → tunggu persetujuan admin.'],
                    ]
                ],
                [
                    'role' => 'Admin',
                    'color' => 'walnut',
                    'icon' => 'shield',
                    'steps' => [
                        ['Login Admin', 'Buka /login → masuk dengan akun admin → otomatis redirect ke /admin/dashboard.'],
                        ['Dashboard Overview', 'Lihat statistik: total pendapatan, jumlah pesanan, produk terlaris, pesanan terbaru.'],
                        ['Kelola Produk', 'Menu Produk → tambah/edit/hapus produk, upload gambar, isi spesifikasi, atur kategori & brand.'],
                        ['Proses Pesanan', 'Menu Pesanan → lihat pesanan masuk → klik "Kelola Pengiriman" → buat resi otomatis via Biteship → cetak label.'],
                        ['Moderasi Ulasan', 'Menu Ulasan → approve/reject ulasan dari customer.'],
                        ['Kelola Retur', 'Menu Retur Barang → lihat pengajuan retur → approve/reject dengan catatan admin.'],
                    ]
                ],
                [
                    'role' => 'Super Admin',
                    'color' => 'gold',
                    'icon' => 'crown',
                    'steps' => [
                        ['Semua Fitur Admin +', 'Super admin memiliki semua fitur admin, ditambah fitur eksklusif berikut:'],
                        ['Kelola Pengguna', 'Menu Pengguna → lihat semua user, suspend/aktifkan akun, hapus akun.'],
                        ['Kelola Kupon', 'Menu Kupon → buat kupon baru (fixed/persen), atur min purchase, masa berlaku.'],
                        ['Kelola Flash Sale', 'Menu Flash Sale → buat event terjadwal, pilih produk & harga diskon, atur stok.'],
                        ['Laporan & Export', 'Menu Laporan → lihat grafik penjualan, produk terlaris, pelanggan → export PDF/Excel.'],
                        ['Audit Log', 'Menu Audit Log → lihat semua aktivitas admin (CRUD, login, perubahan status).'],
                    ]
                ],
            ];
        @endphp

        @foreach($walkthroughs as $wt)
        <div class="space-y-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-{{ $wt['color'] }}-900 text-{{ $wt['color'] === 'gold' ? 'walnut-950' : 'gold-500' }} rounded-xl flex items-center justify-center {{ $wt['color'] === 'gold' ? 'bg-gold-500' : '' }}">
                    <i data-lucide="{{ $wt['icon'] }}" class="w-5 h-5"></i>
                </div>
                <h3 class="font-display text-lg font-black uppercase tracking-tight text-walnut-950">Alur {{ $wt['role'] }}</h3>
            </div>
            <div class="space-y-3 pl-5 border-l-2 border-walnut-800/10">
                @foreach($wt['steps'] as $index => $step)
                <div class="relative pl-6">
                    <div class="absolute -left-[25px] top-1 w-6 h-6 bg-walnut-900 text-gold-500 rounded-full flex items-center justify-center text-[0.55rem] font-black">{{ $index + 1 }}</div>
                    <h4 class="text-xs font-bold uppercase tracking-widest text-walnut-950">{{ $step[0] }}</h4>
                    <p class="text-[0.7rem] text-muted leading-relaxed mt-0.5">{{ $step[1] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB: PENGUJIAN --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div x-show="activeTab === 'testing'" x-transition class="space-y-10">
        <h2 class="font-display text-2xl font-black uppercase tracking-tight text-walnut-950 border-b border-walnut-800/10 pb-3">Panduan Pengujian Sistem</h2>

        {{-- Skenario Pengujian --}}
        <div class="space-y-6">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gold-600">Skenario Pengujian Fungsional</h3>

            @php
                $testCases = [
                    ['Registrasi & Login' => [
                        'TC-01: Registrasi akun baru dengan data valid → akun berhasil dibuat, OTP terkirim ke email.',
                        'TC-02: Registrasi dengan email yang sudah terdaftar → error "Email sudah digunakan".',
                        'TC-03: Login dengan kredensial valid → redirect ke dashboard.',
                        'TC-04: Login dengan password salah → error "Kredensial tidak valid".',
                        'TC-05: Verifikasi OTP dengan kode benar → email terverifikasi.',
                        'TC-06: Reset password via email → link reset terkirim → password berhasil diganti.',
                    ]],
                    ['Katalog & Produk' => [
                        'TC-07: Buka halaman katalog → produk tampil dengan gambar, harga, dan kategori.',
                        'TC-08: Filter produk berdasarkan kategori → hanya produk kategori tersebut yang tampil.',
                        'TC-09: Filter produk berdasarkan brand → hanya produk brand tersebut yang tampil.',
                        'TC-10: Klik produk → halaman detail tampil dengan spesifikasi, galeri, dan review.',
                        'TC-11: Cari produk via search → hasil pencarian relevan ditampilkan.',
                    ]],
                    ['Keranjang & Checkout' => [
                        'TC-12: Tambah produk ke keranjang → item muncul di /cart dengan jumlah benar.',
                        'TC-13: Update jumlah item di cart → subtotal berubah sesuai.',
                        'TC-14: Hapus item dari cart → item hilang dari daftar.',
                        'TC-15: Checkout dengan alamat lengkap → ongkir dihitung otomatis.',
                        'TC-16: Gunakan kupon valid → diskon terpotong di total.',
                        'TC-17: Gunakan kupon expired/invalid → error "Kupon tidak valid".',
                        'TC-18: Proses pembayaran via Midtrans Sandbox → popup muncul → bayar sukses → status "Paid".',
                    ]],
                    ['Pengiriman & Tracking' => [
                        'TC-19: Admin buat resi untuk pesanan "Paid" → resi tergenerate, status → "Processing".',
                        'TC-20: Admin kirim pesanan → status → "Shipped", tracking number muncul.',
                        'TC-21: Customer lacak pesanan → halaman tracking menampilkan progress real-time.',
                        'TC-22: Webhook Biteship update status → status pesanan otomatis berubah.',
                        'TC-23: Simulasi pengiriman via /simulasi → status berubah sesuai aksi.',
                    ]],
                    ['Review & Retur' => [
                        'TC-24: Customer tulis review + upload foto → review masuk ke moderasi admin.',
                        'TC-25: Admin approve review → review tampil di halaman produk.',
                        'TC-26: Admin reject review → review tidak ditampilkan.',
                        'TC-27: Customer ajukan retur → form retur tampil → upload bukti → retur masuk ke admin.',
                        'TC-28: Admin approve/reject retur → status retur berubah, notifikasi terkirim.',
                    ]],
                    ['Admin Panel' => [
                        'TC-29: Admin CRUD produk → produk berhasil ditambah/edit/hapus/restore.',
                        'TC-30: Admin CRUD kategori → kategori berhasil dikelola.',
                        'TC-31: Admin CRUD brand → brand berhasil dikelola.',
                        'TC-32: Super admin buat kupon → kupon bisa digunakan saat checkout.',
                        'TC-33: Super admin buat flash sale → produk muncul dengan harga diskon.',
                        'TC-34: Super admin lihat laporan → grafik dan tabel data tampil benar.',
                        'TC-35: Super admin export PDF/Excel → file terdownload dengan data lengkap.',
                        'TC-36: Activity log mencatat semua aksi admin → audit trail lengkap.',
                    ]],
                ];
            @endphp

            @foreach($testCases as $group)
                @foreach($group as $title => $cases)
                <details class="group bg-cream-50 border border-walnut-800/10 rounded-2xl overflow-hidden" open>
                    <summary class="flex items-center justify-between px-6 py-4 cursor-pointer hover:bg-walnut-50/50 transition">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-walnut-950">{{ $title }}</h4>
                        <span class="text-[0.6rem] font-bold text-muted uppercase tracking-wider">{{ count($cases) }} test cases</span>
                    </summary>
                    <div class="px-6 pb-4 space-y-2">
                        @foreach($cases as $tc)
                        <div class="flex gap-3 items-start py-2 border-t border-walnut-800/5 first:border-t-0">
                            <div class="w-5 h-5 bg-walnut-900/5 rounded flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check-square" class="w-3 h-3 text-walnut-400"></i>
                            </div>
                            <p class="text-[0.7rem] text-walnut-800 leading-relaxed">{{ $tc }}</p>
                        </div>
                        @endforeach
                    </div>
                </details>
                @endforeach
            @endforeach
        </div>

        {{-- Midtrans Sandbox --}}
        <div class="bg-walnut-900 text-cream-50 rounded-3xl p-8 space-y-5">
            <h3 class="font-display text-lg font-black uppercase tracking-widest text-gold-500">Cara Bayar di Midtrans Sandbox</h3>
            <div class="grid md:grid-cols-2 gap-6 text-xs">
                <div class="space-y-3">
                    <h4 class="text-gold-400 font-bold uppercase tracking-widest text-[0.6rem]">Kartu Kredit (Test)</h4>
                    <div class="bg-walnut-950/50 rounded-xl p-4 font-mono text-cream-50/80 space-y-1 text-[0.7rem] border border-cream-50/5">
                        <p>Card Number: <strong class="text-gold-400">4811 1111 1111 1114</strong></p>
                        <p>CVV: <strong class="text-gold-400">123</strong></p>
                        <p>Exp: <strong class="text-gold-400">01/29</strong></p>
                        <p>OTP: <strong class="text-gold-400">112233</strong></p>
                    </div>
                </div>
                <div class="space-y-3">
                    <h4 class="text-gold-400 font-bold uppercase tracking-widest text-[0.6rem]">Virtual Account (Test)</h4>
                    <div class="bg-walnut-950/50 rounded-xl p-4 text-cream-50/80 space-y-1 text-[0.7rem] border border-cream-50/5">
                        <p>Pilih metode <strong class="text-gold-400">Bank Transfer</strong> di popup Midtrans.</p>
                        <p>Pilih bank (BCA/BNI/Mandiri/dll.) → salin nomor VA → klik "Sudah Bayar".</p>
                        <p class="text-cream-50/50">Di mode sandbox, pembayaran otomatis dianggap sukses.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gold-600">Halaman Pengujian Cepat</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <a href="/cekapi" target="_blank" class="flex items-center gap-3 bg-cream-50 border border-walnut-800/10 rounded-2xl p-5 hover:border-gold-500/30 transition group">
                    <div class="w-10 h-10 bg-gold-500/10 text-gold-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-gold-500 group-hover:text-white transition">
                        <i data-lucide="globe" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-widest text-walnut-950">/cekapi</h4>
                        <p class="text-[0.6rem] text-muted">Cek status & limit API eksternal</p>
                    </div>
                </a>
                <a href="/simulasi" target="_blank" class="flex items-center gap-3 bg-cream-50 border border-walnut-800/10 rounded-2xl p-5 hover:border-gold-500/30 transition group">
                    <div class="w-10 h-10 bg-gold-500/10 text-gold-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-gold-500 group-hover:text-white transition">
                        <i data-lucide="play-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-widest text-walnut-950">/simulasi</h4>
                        <p class="text-[0.6rem] text-muted">Simulator webhook Biteship (sandbox)</p>
                    </div>
                </a>
                <a href="/admin/dashboard" target="_blank" class="flex items-center gap-3 bg-cream-50 border border-walnut-800/10 rounded-2xl p-5 hover:border-gold-500/30 transition group">
                    <div class="w-10 h-10 bg-gold-500/10 text-gold-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-gold-500 group-hover:text-white transition">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-widest text-walnut-950">/admin</h4>
                        <p class="text-[0.6rem] text-muted">Dashboard admin panel</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="text-center pt-8 border-t border-walnut-800/10">
        <p class="text-[0.6rem] text-muted uppercase tracking-[0.3em] font-bold">
            DjudasMS Documentation — Generated {{ now()->format('d M Y') }}
        </p>
    </div>
</div>
@endsection
