@extends('layouts.app')

@section('title', 'Tentang Kami - DjudasMS')

@section('content')
<div class="space-y-24 py-12">
    <!-- Hero Section -->
    <section class="text-center space-y-6 max-w-4xl mx-auto">
        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block">Kisah Kami</span>
        <h1 class="font-display text-4xl md:text-6xl font-black uppercase tracking-tighter text-walnut-950">Filosofi <span class="text-gold-500">DjudasMS.</span></h1>
        <p class="text-muted text-lg font-medium leading-relaxed max-w-2xl mx-auto">
            Kami percaya bahwa instrumen bukan sekadar alat, melainkan perpanjangan dari jiwa sang musisi. DjudasMS didirikan untuk memberikan kurasi alat musik premium bagi para penikmat nada sejati.
        </p>
    </section>

    <!-- Image Break -->
    <section class="relative h-[60vh] md:h-[80vh] w-full overflow-hidden">
        <img src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?q=80&w=2070&auto=format&fit=crop" alt="Guitar Workshop" class="absolute inset-0 w-full h-full object-cover mix-blend-multiply opacity-90" />
        <div class="absolute inset-0 bg-gradient-to-t from-cream-100 to-transparent"></div>
    </section>

    <!-- Content Grid -->
    <section class="grid md:grid-cols-2 gap-16 items-center">
        <div class="space-y-6">
            <h2 class="font-display text-3xl font-black uppercase tracking-tight text-walnut-950">Kurasi Tanpa Kompromi</h2>
            <p class="text-muted leading-relaxed text-sm">
                Setiap instrumen yang masuk ke galeri DjudasMS telah melewati inspeksi ketat oleh luthier profesional kami. Kami memastikan setiap detail, mulai dari neck relief, intonasi, hingga finishing, berada dalam kondisi sempurna sebelum instrumen tersebut menyentuh tangan Anda.
            </p>
            <p class="text-muted leading-relaxed text-sm">
                Baik Anda mencari gitar elektrik vintage yang penuh karakter, bass dengan resonansi mendalam, atau amplifier butik yang dapat menangkap nuansa permainan Anda—koleksi kami dirancang untuk menginspirasi.
            </p>
        </div>
        <div class="bg-cream-50 border border-walnut-800/10 p-12 text-center space-y-6">
            <i data-lucide="award" class="w-12 h-12 text-gold-500 mx-auto"></i>
            <h3 class="font-display text-xl font-bold uppercase tracking-widest text-walnut-950">Sertifikasi Keaslian</h3>
            <p class="text-xs text-muted leading-relaxed">
                Setiap produk yang kami jual dilengkapi dengan sertifikat keaslian dan garansi dari distributor resmi. Kami menjamin orisinalitas setiap nada.
            </p>
        </div>
    </section>
</div>
@endsection
