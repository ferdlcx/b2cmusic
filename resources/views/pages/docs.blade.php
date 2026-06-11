@extends('layouts.app')
@section('title', 'Dokumentasi Sistem DjudasMS')

@section('content')
<div class="max-w-[1000px] mx-auto bg-cream-50 p-8 md:p-12 border border-walnut-800/10 shadow-sm animate-fade-in-up">
    
    <div class="border-b border-walnut-800/10 pb-6 mb-8 text-center">
        <h1 class="font-display font-black text-3xl md:text-4xl uppercase tracking-tight text-walnut-950">Dokumentasi Biteship API</h1>
        <p class="text-walnut-500 font-medium mt-2">Panduan Integrasi Logistik & Simulasi Sandbox</p>
    </div>

    <div class="prose prose-walnut max-w-none prose-headings:font-display prose-headings:uppercase prose-headings:tracking-widest prose-a:text-gold-600 hover:prose-a:text-gold-500 prose-img:rounded-xl">
        
        <h2>1. Arsitektur Integrasi Biteship</h2>
        <p>Sistem E-commerce DjudasMS telah terintegrasi penuh dengan <strong>Biteship API</strong> untuk otomatisasi pengiriman. Fitur utama yang diimplementasikan meliputi:</p>
        <ul>
            <li><strong>Pembuatan Pesanan Otomatis (Create Order):</strong> Ketika kustomer menyelesaikan pembayaran, sistem otomatis mengirim data pengiriman ke Biteship.</li>
            <li><strong>Penerimaan Webhook (Status Update):</strong> Biteship akan mengirimkan webhook ke endpoint <code>/api/biteship/webhook</code> secara real-time apabila ada perubahan status kurir.</li>
            <li><strong>Pelacakan Resi (Tracking):</strong> Kustomer dapat melacak resi secara live yang ditarik dari endpoint <code>GET /v1/trackings/:id</code> milik Biteship.</li>
        </ul>

        <hr class="my-10 border-walnut-800/10" />

        <h2>2. Alur Uji Coba (Testing)</h2>
        <p>Karena API Sandbox Biteship memiliki keterbatasan di mana kita <strong>tidak bisa menggunakan API untuk mengubah status pesanan (Advance Status)</strong>, maka DjudasMS merancang dua (2) skenario pengujian yang berbeda sesuai kebutuhan Anda:</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            
            <!-- Skenario 1: Presentasi -->
            <div class="bg-white border border-walnut-800/10 p-6 rounded-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 bg-gold-500 text-white text-[0.6rem] font-bold px-3 py-1 uppercase tracking-widest rounded-bl-xl">Mode Presentasi</div>
                <h3 class="text-lg mt-2 mb-4"><i data-lucide="monitor-play" class="w-5 h-5 inline-block mr-2 mb-1 text-gold-500"></i>Uji Coba Resmi</h3>
                <p class="text-sm leading-relaxed text-walnut-600">
                    Gunakan metode ini saat <strong>Presentasi Formal</strong>. Alur ini membutuhkan Anda untuk login ke dashboard Biteship.
                </p>
                <ol class="text-sm mt-4 space-y-2 list-decimal list-inside text-walnut-700">
                    <li>Buat pesanan fiktif di DjudasMS hingga status pembayaran sukses.</li>
                    <li>Buka <a href="https://dashboard.biteship.com" target="_blank">Dashboard Biteship</a> &raquo; Pastikan toggle <strong>Testing Mode</strong> aktif.</li>
                    <li>Cari pesanan yang baru saja dibuat.</li>
                    <li>Klik <strong>Tombol Kuning (Play Webhook)</strong> pada order tersebut untuk memajukan status pesanan (Alokasi &rarr; Picking Up &rarr; Picked &rarr; Menuju Pelanggan &rarr; Selesai).</li>
                    <li>Biteship akan otomatis mengirimkan Webhook ke server DjudasMS, dan halaman Tracking kustomer akan mengambil data timeline langsung dari server Biteship secara <em>real-time</em>.</li>
                </ol>
            </div>

            <!-- Skenario 2: Simulasi Bebas -->
            <div class="bg-white border border-walnut-800/10 p-6 rounded-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 bg-walnut-900 text-white text-[0.6rem] font-bold px-3 py-1 uppercase tracking-widest rounded-bl-xl">Mode Simulasi Publik</div>
                <h3 class="text-lg mt-2 mb-4"><i data-lucide="users" class="w-5 h-5 inline-block mr-2 mb-1 text-walnut-900"></i>Uji Coba Eksternal (Teman)</h3>
                <p class="text-sm leading-relaxed text-walnut-600">
                    Gunakan metode ini jika Anda ingin teman-teman Anda mencoba simulasi webhook <strong>Tanpa Perlu Login</strong> ke akun Biteship Anda.
                </p>
                <ol class="text-sm mt-4 space-y-2 list-decimal list-inside text-walnut-700">
                    <li>Arahkan teman Anda ke halaman <a href="/simulasi" class="font-bold underline">/simulasi</a>.</li>
                    <li>Halaman ini akan menampilkan daftar pesanan fiktif.</li>
                    <li>Saat tombol <strong>Next Status</strong> diklik, server kita akan mengekstrak ID Tracking & Resi <em>asli</em> dari Sandbox Biteship.</li>
                    <li>Server kemudian membangun <em>Payload Webhook 100% Akurat</em> dan mengirimkannya secara internal ke sistem DjudasMS.</li>
                    <li>Status akan berubah di website, namun di internal Biteship statusnya tetap statis (karena limitasi API Sandbox Biteship).</li>
                </ol>
            </div>

        </div>

        <hr class="my-10 border-walnut-800/10" />

        <h2>3. Status Alur Pengiriman</h2>
        <p>Biteship dan DjudasMS telah menstandarisasi urutan pembaruan status logistik dengan *mapping* internal sebagai berikut:</p>
        
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full text-sm text-left border border-walnut-800/10">
                <thead class="bg-walnut-900 text-gold-500 uppercase tracking-widest text-[0.7rem]">
                    <tr>
                        <th class="px-6 py-4">Biteship Status (Internal)</th>
                        <th class="px-6 py-4">Nama Status (UI DjudasMS)</th>
                        <th class="px-6 py-4">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-walnut-800/10 bg-white">
                    <tr>
                        <td class="px-6 py-4 font-mono font-bold text-walnut-900">allocated</td>
                        <td class="px-6 py-4 font-semibold text-walnut-900">Alocate</td>
                        <td class="px-6 py-4 text-walnut-600">Kurir telah dialokasikan untuk penjemputan.</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 font-mono font-bold text-walnut-900">picking_up</td>
                        <td class="px-6 py-4 font-semibold text-walnut-900">Picking Up</td>
                        <td class="px-6 py-4 text-walnut-600">Kurir sedang menuju lokasi pickup (gudang).</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 font-mono font-bold text-walnut-900">picked</td>
                        <td class="px-6 py-4 font-semibold text-walnut-900">Barang Dijemput</td>
                        <td class="px-6 py-4 text-walnut-600">Barang telah diserahkan dari gudang ke tangan kurir.</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 font-mono font-bold text-walnut-900">dropping_off</td>
                        <td class="px-6 py-4 font-semibold text-walnut-900">Menuju Pelanggan</td>
                        <td class="px-6 py-4 text-walnut-600">Barang sedang dalam perjalanan menuju alamat kustomer.</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 font-mono font-bold text-emerald-600">delivered</td>
                        <td class="px-6 py-4 font-semibold text-emerald-600">Selesai</td>
                        <td class="px-6 py-4 text-emerald-600">Barang berhasil diterima oleh kustomer.</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 font-mono font-bold text-rose-600">return_in_transit</td>
                        <td class="px-6 py-4 font-semibold text-rose-600">Return Process</td>
                        <td class="px-6 py-4 text-rose-600">Pengiriman gagal, barang dikembalikan ke gudang asal.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    </div>
</div>
@endsection
