@extends('layouts.app')

@section('title', 'Hubungi Kami - DjudasMS')

@section('content')
<div class="space-y-16 py-12">
    <!-- Header -->
    <div class="text-center space-y-4 max-w-2xl mx-auto border-b border-walnut-800/10 pb-12">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Dukungan Pelanggan</span>
        <h1 class="font-display text-4xl md:text-5xl font-black uppercase tracking-tighter text-walnut-950">Hubungi <span class="text-gold-500">Kami.</span></h1>
        <p class="text-muted text-sm font-medium leading-relaxed">
            Punya pertanyaan mengenai spesifikasi instrumen atau butuh bantuan dengan pesanan Anda? Tim ahli kami siap membantu.
        </p>
    </div>

    <!-- Contact Grid -->
    <div class="grid lg:grid-cols-2 gap-16 max-w-5xl mx-auto">
        
        <!-- Contact Info -->
        <div class="space-y-12">
            <div>
                <h3 class="font-display text-xl font-bold uppercase tracking-tight text-walnut-950 mb-6">Informasi Kontak</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <i data-lucide="map-pin" class="w-5 h-5 text-gold-500 flex-shrink-0 mt-1"></i>
                        <div>
                            <span class="text-[0.7rem] uppercase tracking-widest font-bold text-walnut-900 block mb-1">Lokasi Galeri</span>
                            <p class="text-sm text-muted leading-relaxed">Jl. Musik Harmoni No. 12<br>Kebayoran Baru, Jakarta Selatan<br>Indonesia 12110</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <i data-lucide="phone" class="w-5 h-5 text-gold-500 flex-shrink-0 mt-1"></i>
                        <div>
                            <span class="text-[0.7rem] uppercase tracking-widest font-bold text-walnut-900 block mb-1">Telepon</span>
                            <p class="text-sm text-muted leading-relaxed">+62 21 555 1234</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <i data-lucide="mail" class="w-5 h-5 text-gold-500 flex-shrink-0 mt-1"></i>
                        <div>
                            <span class="text-[0.7rem] uppercase tracking-widest font-bold text-walnut-900 block mb-1">Surel</span>
                            <p class="text-sm text-muted leading-relaxed">concierge@djudasms.com</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-display text-xl font-bold uppercase tracking-tight text-walnut-950 mb-6">Jam Operasional</h3>
                <div class="space-y-2 text-sm text-muted">
                    <div class="flex justify-between border-b border-walnut-800/10 pb-2">
                        <span>Senin - Jumat</span>
                        <span class="font-bold text-walnut-900">10:00 - 20:00</span>
                    </div>
                    <div class="flex justify-between border-b border-walnut-800/10 pb-2">
                        <span>Sabtu</span>
                        <span class="font-bold text-walnut-900">10:00 - 18:00</span>
                    </div>
                    <div class="flex justify-between pb-2">
                        <span>Minggu</span>
                        <span class="font-bold text-walnut-900">Tutup</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-cream-50 border border-walnut-800/10 p-8">
            <h3 class="font-display text-xl font-bold uppercase tracking-tight text-walnut-950 mb-8">Kirim Pesan</h3>
            <form action="#" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="text-[0.65rem] uppercase tracking-widest font-bold text-walnut-800 block mb-2">Nama Lengkap</label>
                    <input type="text" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.8rem]" required>
                </div>
                <div>
                    <label class="text-[0.65rem] uppercase tracking-widest font-bold text-walnut-800 block mb-2">Alamat Surel</label>
                    <input type="email" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.8rem]" required>
                </div>
                <div>
                    <label class="text-[0.65rem] uppercase tracking-widest font-bold text-walnut-800 block mb-2">Pesan</label>
                    <textarea rows="4" class="w-full bg-transparent border-b border-walnut-800/20 py-2.5 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-[0.8rem]" required></textarea>
                </div>
                <button type="submit" class="w-full py-4 bg-walnut-900 text-gold-500 font-bold uppercase text-[0.7rem] tracking-[0.2em] hover:bg-gold-600 hover:text-white transition duration-500">
                    Kirim Pertanyaan
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
