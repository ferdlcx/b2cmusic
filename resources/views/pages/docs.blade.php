@extends('layouts.app')

@section('title', 'Dokumentasi Sistem - DjudasMS')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-6">
    <div class="space-y-4 mb-12">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Developer Reference</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">Arsitektur <span class="text-gold-500">Sistem.</span></h1>
        <p class="text-muted leading-relaxed max-w-2xl">Penjelasan lengkap mengenai cara kerja platform DjudasMS, integrasi pihak ketiga, dan siklus hidup pesanan.</p>
    </div>

    <div class="prose prose-slate max-w-none prose-headings:font-display prose-headings:uppercase prose-headings:tracking-tight prose-a:text-gold-600">
        
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8">
            <h4 class="text-blue-800 font-bold m-0 mb-1 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4"></i> Konsep Hybrid (Real vs Simulasi)
            </h4>
            <p class="text-sm text-blue-900 m-0">Sistem ini menggunakan arsitektur hybrid yang menggabungkan API sungguhan (Midtrans Sandbox, Biteship Test, RajaOngkir) dengan simulasi lokal. Ini memungkinkan testing end-to-end tanpa memerlukan pembayaran asli atau kurir nyata.</p>
        </div>

        <h3 class="text-2xl font-black text-walnut-950 border-b pb-2">1. Tech Stack</h3>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mt-4">
            <li class="bg-cream-50 p-4 rounded-xl border border-walnut-800/10"><strong class="block text-walnut-950">Framework:</strong> Laravel 13.x</li>
            <li class="bg-cream-50 p-4 rounded-xl border border-walnut-800/10"><strong class="block text-walnut-950">Frontend:</strong> Blade + TailwindCSS + Alpine.js</li>
            <li class="bg-cream-50 p-4 rounded-xl border border-walnut-800/10"><strong class="block text-walnut-950">Payment:</strong> Midtrans (Snap API & Webhooks)</li>
            <li class="bg-cream-50 p-4 rounded-xl border border-walnut-800/10"><strong class="block text-walnut-950">Logistik:</strong> Biteship (Order Creation & Webhooks)</li>
            <li class="bg-cream-50 p-4 rounded-xl border border-walnut-800/10"><strong class="block text-walnut-950">Ongkir:</strong> RajaOngkir Komerce API</li>
            <li class="bg-cream-50 p-4 rounded-xl border border-walnut-800/10"><strong class="block text-walnut-950">Notifikasi:</strong> Database + MailerSend</li>
        </ul>

        <h3 class="text-2xl font-black text-walnut-950 border-b pb-2 mt-12">2. Flow Pemesanan & Pengiriman</h3>
        <p>Siklus hidup pesanan sepenuhnya otomatis. Admin toko tidak perlu mengubah status secara manual.</p>
        
        <div class="bg-walnut-900 p-6 rounded-xl my-6 text-cream-50 overflow-x-auto">
            <pre class="m-0 text-sm font-mono text-gold-400">
[ 1. Checkout ]
      ↓
(Sistem auto-pilih kurir termurah via RajaOngkir)
      ↓
[ 2. Order dibuat (Pending) ]
      ↓
(User bayar via Midtrans)
      ↓
[ 3. Midtrans Webhook (Settlement) ]
      ↓
(Sistem otomatis ubah status order menjadi 'Processing')
(Sistem otomatis inisialisasi tracking history)
(Sistem otomatis buat order pengiriman di Biteship)
      ↓
[ 4. Biteship Webhook / Simulasi Lokal ]
      ↓
(Status tracking terus bertambah: Confirmed → Picking Up → Dropping Off)
(Map tracing otomatis memperbarui lokasi)
      ↓
[ 5. Delivered ]
      ↓
(Sistem ubah status order menjadi 'Completed')
            </pre>
        </div>

        <h3 class="text-2xl font-black text-walnut-950 border-b pb-2 mt-12">3. History-Based Tracking</h3>
        <p>Sistem tracking menggunakan pendekatan <strong>append-only array</strong> (JSON) di dalam tabel <code>shipments.status_history</code>. Setiap update dari kurir tidak akan menghapus status sebelumnya, melainkan menambah entri baru. Ini memastikan pelanggan bisa melihat riwayat lengkap perjalanan paket dari gudang hingga sampai ke tujuan, persis seperti e-commerce besar.</p>

        <h3 class="text-2xl font-black text-walnut-950 border-b pb-2 mt-12">4. Role Based Access</h3>
        <ul>
            <li><strong>Customer:</strong> Berbelanja, checkout, membayar, dan melacak pesanan.</li>
            <li><strong>Admin / Staff:</strong> Hanya bertugas melihat pesanan masuk dan menyiapkan paket secara fisik di gudang. Sistem tracking sudah berjalan otomatis via webhook kurir, sehingga admin tidak perlu "update resi" secara manual.</li>
        </ul>
        
        <div class="mt-12 text-center">
            <a href="{{ route('doctest') }}" class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-gold-600 text-walnut-950 text-[0.75rem] font-bold tracking-[0.2em] uppercase hover:bg-gold-500 transition-all duration-300 shadow-[0_0_20px_rgba(212,160,23,0.3)]">
                Lanjut ke Tutorial Testing
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</div>
@endsection
