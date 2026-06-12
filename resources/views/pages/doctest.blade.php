@extends('layouts.app')

@section('title', 'Tutorial Testing & Simulasi - DjudasMS')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-6">
    <div class="space-y-4 mb-12">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Developer Resources</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">Tutorial <span class="text-gold-500">Testing.</span></h1>
        <p class="text-muted leading-relaxed max-w-2xl">Panduan step-by-step untuk melakukan simulasi transaksi dan pengiriman tanpa harus menggunakan uang asli atau resi asli.</p>
    </div>

    <div class="prose prose-slate max-w-none prose-headings:font-display prose-headings:uppercase prose-headings:tracking-tight prose-a:text-gold-600">
        
        <h3 class="text-2xl font-black text-walnut-950 border-b pb-2">1. Simulasi Pembayaran (Midtrans Sandbox)</h3>
        <p>Anda dapat menguji proses pembayaran dengan menggunakan Midtrans Simulator.</p>
        <ol class="space-y-2">
            <li>Lakukan pemesanan barang seperti biasa hingga mencapai halaman Midtrans Snap (Pilih metode pembayaran).</li>
            <li>Pilih salah satu metode pembayaran, misalnya <strong>BCA Virtual Account</strong>.</li>
            <li>Copy nomor Virtual Account yang muncul di layar.</li>
            <li>Buka tab baru dan akses <a href="https://simulator.sandbox.midtrans.com/" target="_blank" class="font-bold">Midtrans Simulator</a>.</li>
            <li>Pilih tipe simulasi yang sesuai (misal: BCA Virtual Account), paste nomor VA tadi, dan klik Inquire / Pay.</li>
            <li>Kembali ke tab toko, halaman akan otomatis dialihkan ke status Berhasil. Status pesanan di database akan otomatis menjadi <strong>Processing</strong>.</li>
        </ol>

        <h3 class="text-2xl font-black text-walnut-950 border-b pb-2 mt-12">2. Simulasi Pengiriman Lokal (Sandbox Tracker)</h3>
        <p>Jika pesanan menggunakan simulasi (atau Anda tidak menggunakan resi asli Biteship), Anda dapat menjalankan simulasi status tracker secara manual melalui Sandbox.</p>
        <ol class="space-y-2">
            <li>Buka halaman <a href="{{ route('simulasi') }}" target="_blank" class="font-bold">/simulasi</a> di browser Anda.</li>
            <li>Cari pesanan Anda yang berstatus <strong>Processing</strong>.</li>
            <li>Klik tombol <span class="bg-gold-500 text-white px-2 py-0.5 text-xs rounded">Kirim Pesanan</span>. Sistem akan men-generate resi SIM-RESI-XXX dan memulai tracking history.</li>
            <li>Di tabel pesanan tersebut, akan muncul tombol-tombol webhook status (Allocated, Picking Up, Picked, Dropping Off, Delivered).</li>
            <li>Klik tombol-tombol tersebut secara berurutan.</li>
            <li>Periksa halaman <a href="{{ route('orders.history') }}" target="_blank">Pesanan Saya</a> sebagai Customer, lalu klik "Lacak".</li>
            <li>Anda akan melihat map dan history tracking bertambah secara real-time untuk setiap tombol status yang Anda klik. Label <strong>[TEST MODE]</strong> akan muncul pada notifikasi dan history.</li>
        </ol>

        <h3 class="text-2xl font-black text-walnut-950 border-b pb-2 mt-12">3. Notifikasi</h3>
        <p>Setiap perubahan status pembayaran dan pengiriman akan mengirimkan notifikasi. Anda dapat mengeceknya di menu lonceng notifikasi (sudut kanan atas) atau halaman <a href="{{ route('notifications.index') }}" target="_blank">Notifikasi Saya</a>. Jika simulasi, notifikasi akan memiliki label warna merah <strong>[TEST MODE]</strong>.</p>
    </div>
</div>
@endsection
