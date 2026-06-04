<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'DjudasMS')</title>
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Alpine.js & Lucide Icons -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                            display: ['Outfit', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        /* Floating Orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float-orb 20s ease-in-out infinite;
        }
        .orb-1 {
            width: 600px; height: 600px;
            background: linear-gradient(135deg, #6366f1, #a78bfa);
            top: -10%; left: -5%;
            animation-delay: 0s;
            animation-duration: 25s;
        }
        .orb-2 {
            width: 500px; height: 500px;
            background: linear-gradient(135deg, #ec4899, #f97316);
            top: 50%; right: -10%;
            animation-delay: -8s;
            animation-duration: 30s;
        }
        .orb-3 {
            width: 400px; height: 400px;
            background: linear-gradient(135deg, #06b6d4, #10b981);
            bottom: -10%; left: 30%;
            animation-delay: -15s;
            animation-duration: 22s;
        }
        @keyframes float-orb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(80px, -60px) scale(1.1); }
            50% { transform: translate(-40px, 80px) scale(0.95); }
            75% { transform: translate(60px, 40px) scale(1.05); }
        }

        /* Fade-in-up animation */
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        /* Scroll indicator bounce */
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0) translateX(-50%); }
            50% { transform: translateY(8px) translateX(-50%); }
        }
        .animate-bounce-slow { animation: bounce-slow 2.5s ease-in-out infinite; }

        /* Scroll dot inside indicator */
        @keyframes scroll-dot {
            0% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(12px); }
        }
        .animate-scroll-dot { animation: scroll-dot 1.8s ease-in-out infinite; }

        /* Card shine hover effect */
        .card-shine {
            position: relative;
            overflow: hidden;
        }
        .card-shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(transparent, rgba(255,255,255,0.05), transparent);
            transform: rotate(30deg);
            transition: 0.6s;
            opacity: 0;
        }
        .card-shine:hover::after {
            opacity: 1;
            transform: rotate(30deg) translateY(-30%);
        }

        /* ===== GLOBAL DYNAMIC ENHANCEMENTS ===== */

        /* Premium Button Hover Effects */
        a[class*='bg-indigo-600']:hover,
        button[class*='bg-indigo-600']:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -8px rgba(79, 70, 229, 0.4);
        }
        a[class*='bg-indigo-600'],
        button[class*='bg-indigo-600'] {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        /* Cards - Global glass/lift effect */
        [class*='rounded-3xl'][class*='border'][class*='bg-white'],
        [class*='rounded-[32px]'][class*='border'][class*='bg-white'],
        [class*='rounded-[36px]'][class*='border'][class*='bg-white'],
        [class*='rounded-2xl'][class*='border'][class*='bg-white'] {
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1) !important;
            background: rgba(255,255,255,0.85) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
        }
        [class*='rounded-3xl'][class*='border'][class*='bg-white']:hover,
        [class*='rounded-[32px]'][class*='border'][class*='bg-white']:hover,
        [class*='rounded-[36px]'][class*='border'][class*='bg-white']:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px -12px rgba(0,0,0,0.1), 0 0 0 1px rgba(99, 102, 241, 0.08);
        }

        /* Image zoom on hover inside cards */
        [class*='overflow-hidden'] img {
            transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        [class*='overflow-hidden']:hover img {
            transform: scale(1.06);
        }

        /* Badge/Tag pulse glow */
        [class*='bg-indigo-50'][class*='text-indigo-600'] {
            transition: all 0.3s ease;
        }
        [class*='bg-indigo-50'][class*='text-indigo-600']:hover {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.15);
        }

        /* Status badge micro-animation */
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Input focus glow */
        input:focus, select:focus, textarea:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12) !important;
            transition: box-shadow 0.3s ease, border-color 0.3s ease !important;
        }

        /* Smooth section dividers */
        section + section {
            position: relative;
        }

        /* Typography - subtle text gradient on headings */
        .font-display {
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }

        /* Staggered animation for grid children */
        @keyframes stagger-in {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .grid > * {
            animation: stagger-in 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .grid > *:nth-child(1) { animation-delay: 0.05s; }
        .grid > *:nth-child(2) { animation-delay: 0.1s; }
        .grid > *:nth-child(3) { animation-delay: 0.15s; }
        .grid > *:nth-child(4) { animation-delay: 0.2s; }
        .grid > *:nth-child(5) { animation-delay: 0.25s; }
        .grid > *:nth-child(6) { animation-delay: 0.3s; }
        .grid > *:nth-child(7) { animation-delay: 0.35s; }
        .grid > *:nth-child(8) { animation-delay: 0.4s; }
        .grid > *:nth-child(9) { animation-delay: 0.45s; }
        .grid > *:nth-child(10) { animation-delay: 0.5s; }
        .grid > *:nth-child(11) { animation-delay: 0.55s; }
        .grid > *:nth-child(12) { animation-delay: 0.6s; }

        /* Page container subtle entrance */
        .max-w-\[1440px\] > main,
        .max-w-\[1440px\] > .space-y-10 {
            animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Footer lift on scroll */
        footer {
            transition: transform 0.5s ease;
        }

        /* Toast/Alert notification animation */
        [class*='bg-emerald-50'][class*='border-emerald'],
        [class*='bg-rose-50'][class*='border-rose'],
        [class*='bg-amber-50'][class*='border-amber'] {
            animation: fade-in-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Smooth scrollbar for webkit */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #a5b4fc, #6366f1);
            border-radius: 100px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #818cf8, #4f46e5);
        }

        /* Selection color */
        ::selection {
            background: rgba(99, 102, 241, 0.2);
            color: #312e81;
        }
    </style>
</head>
<body class="font-sans text-slate-900 bg-[#f8f8f8] min-h-screen flex flex-col justify-between overflow-x-hidden">
    <!-- Floating Orbs Background -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div>
        <!-- Navbar -->
        <header x-data="{ mobileMenuOpen: false }" class="bg-white/80 backdrop-blur-xl border-b border-slate-100/85 sticky top-0 z-50 transition duration-300">
            <div class="max-w-[1440px] mx-auto px-6 lg:px-10 py-4 flex items-center justify-between">
                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none transition">
                    <template x-if="!mobileMenuOpen">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </template>
                    <template x-if="mobileMenuOpen">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </template>
                </button>

                <!-- Logo -->
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-slate-900 hover:opacity-90 transition">
                    <span class="font-display font-black text-xl tracking-tight uppercase flex items-center gap-1.5">
                        <i data-lucide="music-4" class="w-6 h-6 text-indigo-600"></i>
                        DjudasMS <span class="text-indigo-600 font-semibold text-[0.7rem] tracking-[0.25em] self-end mb-0.5 hidden sm:inline-block">LUXE</span>
                    </span>
                </a>

                <!-- Navigation Links (Desktop) -->
                <nav class="hidden md:flex items-center gap-8 text-[0.85rem] font-medium tracking-wider text-slate-600">
                    <a href="{{ route('home') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5 {{ request()->routeIs('home') ? 'text-indigo-600 font-bold' : '' }}">
                        <i data-lucide="home" class="w-4 h-4"></i> Home
                    </a>
                    <a href="{{ route('catalog') }}" class="hover:text-indigo-600 transition flex items-center gap-1.5 {{ request()->routeIs('catalog') ? 'text-indigo-600 font-bold' : '' }}">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i> Shop Catalog
                    </a>
                </nav>

                <!-- Auth & Cart Actions -->
                <div class="flex items-center gap-4">
                    @auth
                        <!-- Cart -->
                        <a href="{{ route('cart.index') }}" class="relative p-2.5 rounded-2xl hover:bg-slate-100 text-slate-600 hover:text-indigo-600 transition" title="Keranjang Belanja">
                            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                            @php
                                $cartCount = \App\Models\CartItem::whereHas('cart', function($q) {
                                    $q->where('user_id', auth()->id());
                                })->sum('quantity');
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute top-1 right-1 bg-indigo-600 text-white text-[0.6rem] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center border border-white">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Orders (Desktop Only) -->
                        <a href="{{ route('orders.history') }}" class="hidden md:inline-flex p-2.5 rounded-2xl hover:bg-slate-100 text-slate-600 hover:text-indigo-600 transition" title="Riwayat Pesanan">
                            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                        </a>

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                            <a href="{{ route('admin.dashboard') }}" class="hidden md:inline-flex text-xs font-semibold tracking-wider bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1.5 rounded-xl hover:bg-indigo-600 hover:text-white transition">Admin</a>
                        @endif

                        <!-- User Profile Dropdown (Desktop Only) -->
                        <div x-data="{ open: false }" @click.away="open = false" class="relative hidden md:block">
                            <button @click="open = !open" class="flex items-center gap-1.5 focus:outline-none hover:text-indigo-600 transition py-1.5 px-2.5 rounded-xl hover:bg-slate-100">
                                <div class="w-7 h-7 bg-indigo-100 text-indigo-700 flex items-center justify-center rounded-full text-xs font-bold uppercase">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="text-xs font-semibold text-slate-700 hidden lg:inline">{{ explode(' ', auth()->user()->name)[0] }}</span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                            </button>
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="opacity-0 scale-95" 
                                 x-transition:enter-end="opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-75" 
                                 x-transition:leave-start="opacity-100 scale-100" 
                                 x-transition:leave-end="opacity-0 scale-95" 
                                 class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 z-50"
                                 style="display: none;">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-[0.65rem] text-slate-400 uppercase tracking-wider font-bold">Pengguna</p>
                                    <p class="text-xs font-semibold text-slate-800 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                                    <a href="{{ route('admin.dashboard') }}" class="flex md:hidden items-center gap-2 px-4 py-2 text-sm text-indigo-600 hover:bg-indigo-50">
                                        <i data-lucide="shield-check" class="w-4 h-4"></i> Panel Admin
                                    </a>
                                @endif
                                <a href="{{ route('profile.show') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                                    <i data-lucide="user" class="w-4 h-4"></i> Profil Saya
                                </a>
                                <a href="{{ route('orders.history') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                                    <i data-lucide="package" class="w-4 h-4"></i> Pesanan Saya
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-rose-50 text-left">
                                        <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden md:inline-flex text-slate-600 hover:text-indigo-600 text-xs font-semibold tracking-wider uppercase transition">Masuk</a>
                        <a href="{{ route('register') }}" class="hidden md:inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-2xl text-xs font-semibold tracking-wider hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300">Daftar</a>
                    @endauth
                </div>
            </div>

            <!-- Mobile Navigation Links -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200" 
                 x-transition:enter-start="opacity-0 -translate-y-4" 
                 x-transition:enter-end="opacity-100 translate-y-0" 
                 x-transition:leave="transition ease-in duration-150" 
                 x-transition:leave-start="opacity-100 translate-y-0" 
                 x-transition:leave-end="opacity-0 -translate-y-4" 
                 class="md:hidden bg-white border-b border-slate-100 px-6 py-4 space-y-3 shadow-lg"
                 style="display: none;">
                <a href="{{ route('home') }}" class="flex items-center gap-3 py-2 text-sm font-medium text-slate-600 hover:text-indigo-600 transition">
                    <i data-lucide="home" class="w-5 h-5"></i> Home
                </a>
                <a href="{{ route('catalog') }}" class="flex items-center gap-3 py-2 text-sm font-medium text-slate-600 hover:text-indigo-600 transition">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i> Shop Catalog
                </a>
                @auth
                    <hr class="border-slate-100 my-2" />
                    <a href="{{ route('orders.history') }}" class="flex items-center gap-3 py-2 text-sm font-medium text-slate-600 hover:text-indigo-600 transition">
                        <i data-lucide="clipboard-list" class="w-5 h-5"></i> Riwayat Pesanan
                    </a>
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 py-2 text-sm font-medium text-slate-600 hover:text-indigo-600 transition">
                        <i data-lucide="user" class="w-5 h-5"></i> Profil Saya
                    </a>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
                            <i data-lucide="shield-check" class="w-5 h-5"></i> Panel Admin
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="w-full pt-2">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 py-2 text-sm font-medium text-red-600 hover:text-red-700 transition text-left">
                            <i data-lucide="log-out" class="w-5 h-5"></i> Keluar
                        </button>
                    </form>
                @else
                    <hr class="border-slate-100 my-2" />
                    <a href="{{ route('login') }}" class="flex items-center gap-3 py-2 text-sm font-medium text-slate-600 hover:text-indigo-600 transition">
                        <i data-lucide="log-in" class="w-5 h-5"></i> Masuk
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center gap-3 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
                        <i data-lucide="user-plus" class="w-5 h-5"></i> Daftar
                    </a>
                @endauth
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="max-w-[1440px] mx-auto px-6 lg:px-10 mt-6">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-[0_10px_30px_rgba(16,185,129,0.05)]">
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-xs font-bold uppercase ml-4">Close</button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-[1440px] mx-auto px-6 lg:px-10 mt-6">
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl flex items-center justify-between shadow-[0_10px_30px_rgba(244,63,94,0.05)]">
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-xs font-bold uppercase ml-4">Close</button>
                </div>
            </div>
        @endif

        <!-- Content -->
        <main class="max-w-[1440px] mx-auto px-6 lg:px-10 py-8">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-16 text-slate-500 mt-20">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 pb-12 border-b border-slate-200">
                <div class="space-y-4">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3 uppercase text-sm tracking-[0.55em] text-slate-900">
                        <span class="font-black text-lg">MUSICSTORE</span>
                        <span class="text-slate-500 text-[0.75rem] tracking-[0.7em]">LUXE</span>
                    </a>
                    <p class="text-sm text-slate-400 max-w-sm leading-relaxed">Destinasi belanja instrumen musik premium, vinyl vintage, dan perlengkapan studio murni dengan kualitas desain editorial.</p>
                </div>
                <div class="grid grid-cols-2 gap-6 text-sm uppercase tracking-[0.2em]">
                    <div class="space-y-4">
                        <div class="font-semibold text-slate-900">Kategori</div>
                        <div><a href="{{ route('catalog', ['category' => 'gitar-akustik']) }}" class="hover:text-slate-900">Gitar</a></div>
                        <div><a href="{{ route('catalog', ['category' => 'keyboard-piano']) }}" class="hover:text-slate-900">Keyboard & Piano</a></div>
                        <div><a href="{{ route('catalog', ['category' => 'audio-recording']) }}" class="hover:text-slate-900">Audio Gear</a></div>
                    </div>
                    <div class="space-y-4">
                        <div class="font-semibold text-slate-900">Toko</div>
                        <div><a href="{{ route('catalog') }}" class="hover:text-slate-900">Semua Produk</a></div>
                        <div><a href="#" class="hover:text-slate-900">Tentang Kami</a></div>
                        <div><a href="#" class="hover:text-slate-900">Kontak</a></div>
                    </div>
                </div>
                <div class="space-y-4 text-sm uppercase tracking-[0.2em]">
                    <div class="font-semibold text-slate-900">Ikuti Kami</div>
                    <div class="flex gap-4">
                        <a href="#" class="hover:text-slate-900">Instagram</a>
                        <a href="#" class="hover:text-slate-900">YouTube</a>
                        <a href="#" class="hover:text-slate-900">TikTok</a>
                    </div>
                </div>
            </div>
            <div class="pt-8 text-center text-xs text-slate-400 tracking-wider">
                &copy; {{ date('Y') }} DjudasMS. Dibuat untuk Tugas Kuliah. model B2C E-commerce.
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            // Scroll Animation Observer
            const sections = document.querySelectorAll('section, .scroll-reveal');
            sections.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(40px)';
                el.style.transition = 'opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1)';
            });

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            sections.forEach(el => observer.observe(el));
        });
    </script>

<!-- Floating Chat CS Widget -->
<div x-data="chatWidget()" class="fixed bottom-6 right-6 z-[100]" x-cloak>
    <!-- Chat Window -->
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90 translate-y-4"
        class="absolute bottom-20 right-0 w-[360px] max-h-[520px] bg-white/95 backdrop-blur-xl border border-slate-200/80 rounded-3xl shadow-2xl overflow-hidden flex flex-col">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-5 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
                        <i data-lucide="headphones" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm">DjudasMS Support</h4>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                            <span class="text-[0.65rem] text-white/80 font-medium">Online sekarang</span>
                        </div>
                    </div>
                </div>
                <button @click="isOpen = false" class="w-8 h-8 hover:bg-white/10 rounded-lg flex items-center justify-center transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 max-h-[300px]" x-ref="chatMessages">
            <template x-for="(msg, idx) in messages" :key="idx">
                <div :class="msg.from === 'bot' ? 'flex justify-start' : 'flex justify-end'">
                    <div :class="msg.from === 'bot' ? 'bg-slate-100 text-slate-800 rounded-2xl rounded-tl-md' : 'bg-indigo-600 text-white rounded-2xl rounded-tr-md'" class="px-4 py-2.5 max-w-[85%] text-[0.8rem] leading-relaxed font-medium shadow-sm">
                        <span x-html="msg.text"></span>
                    </div>
                </div>
            </template>
            <div x-show="isTyping" class="flex justify-start">
                <div class="bg-slate-100 text-slate-500 rounded-2xl rounded-tl-md px-4 py-3 text-xs font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0s"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="px-4 pb-2" x-show="messages.length <= 1">
            <div class="flex flex-wrap gap-1.5">
                <button @click="sendQuickQuestion('Cara bayar?')" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-[0.65rem] font-bold hover:bg-indigo-100 transition">Cara bayar?</button>
                <button @click="sendQuickQuestion('Cara retur?')" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-[0.65rem] font-bold hover:bg-indigo-100 transition">Cara retur?</button>
                <button @click="sendQuickQuestion('Jam operasional?')" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-[0.65rem] font-bold hover:bg-indigo-100 transition">Jam operasional?</button>
                <button @click="sendQuickQuestion('Ongkir berapa?')" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-full text-[0.65rem] font-bold hover:bg-indigo-100 transition">Ongkir?</button>
                <button @click="sendQuickQuestion('Hubungi CS')" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-full text-[0.65rem] font-bold hover:bg-emerald-100 transition">💬 Hubungi CS</button>
            </div>
        </div>

        <!-- Input -->
        <div class="p-3 border-t border-slate-100">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input x-model="userInput" type="text" placeholder="Ketik pertanyaan Anda..." class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition" />
                <button type="submit" class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center hover:bg-indigo-700 transition shrink-0">
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Floating Button -->
    <button @click="toggleChat()" 
        class="w-14 h-14 bg-gradient-to-br from-indigo-600 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl hover:shadow-indigo-600/30 hover:scale-110 transition-all duration-300 flex items-center justify-center relative">
        <i data-lucide="message-circle" class="w-6 h-6" x-show="!isOpen"></i>
        <i data-lucide="x" class="w-6 h-6" x-show="isOpen"></i>
        <span x-show="!isOpen && unread > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white text-[0.6rem] font-bold rounded-full flex items-center justify-center animate-bounce" x-text="unread"></span>
    </button>
</div>

<script>
function chatWidget() {
    return {
        isOpen: false,
        isTyping: false,
        unread: 1,
        userInput: '',
        messages: [
            { from: 'bot', text: 'Halo! 👋 Selamat datang di <strong>DjudasMS Support</strong>. Ada yang bisa saya bantu hari ini?' }
        ],
        faq: [
            { keywords: ['bayar', 'pembayaran', 'payment', 'transfer', 'qris'], answer: '💳 <strong>Cara Pembayaran:</strong><br>1. Pilih produk & checkout<br>2. Pilih metode: QRIS, VA, E-Wallet, atau Kartu Kredit<br>3. Selesaikan pembayaran via Midtrans<br>4. Status otomatis ter-update!' },
            { keywords: ['retur', 'kembalikan', 'refund', 'pengembalian', 'return'], answer: '📦 <strong>Cara Retur Barang:</strong><br>1. Buka halaman Detail Pesanan (status: Selesai)<br>2. Klik tombol "Ajukan Pengembalian"<br>3. Isi alasan & upload foto bukti<br>4. Tunggu persetujuan Admin (1-3 hari kerja)' },
            { keywords: ['jam', 'operasional', 'buka', 'tutup', 'waktu'], answer: '🕐 <strong>Jam Operasional:</strong><br>Senin - Jumat: 09.00 - 21.00 WIB<br>Sabtu - Minggu: 10.00 - 18.00 WIB<br>Chat bot ini tersedia 24/7!' },
            { keywords: ['ongkir', 'kirim', 'pengiriman', 'shipping', 'kurir', 'jne', 'jnt'], answer: '🚚 <strong>Info Pengiriman:</strong><br>Kami menggunakan JNE, J&T, dan POS Indonesia. Ongkir dihitung otomatis berdasarkan berat & lokasi Anda saat checkout.' },
            { keywords: ['batalkan', 'batal', 'cancel', 'hapus'], answer: '❌ <strong>Batalkan Pesanan:</strong><br>Anda bisa membatalkan pesanan yang masih berstatus "Pending". Buka halaman Detail Pesanan → klik tombol merah "Batalkan Pesanan".' },
            { keywords: ['garansi', 'warranty', 'rusak'], answer: '🛡️ <strong>Garansi:</strong><br>Semua produk DjudasMS dilindungi garansi resmi 1 tahun. Jika ada kerusakan, ajukan retur melalui halaman pesanan Anda.' },
            { keywords: ['hubungi', 'cs', 'customer service', 'whatsapp', 'wa', 'telepon', 'kontak'], answer: '📞 <strong>Hubungi CS Kami:</strong><br>Untuk pertanyaan lebih lanjut, silakan hubungi CS kami via WhatsApp:<br><a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-1 mt-2 px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-xs font-bold hover:bg-emerald-600 transition">💬 Chat WhatsApp</a>' },
            { keywords: ['halo', 'hai', 'hi', 'hey', 'hello'], answer: 'Halo juga! 😊 Silakan tanyakan apa saja seputar DjudasMS. Saya siap membantu!' },
            { keywords: ['terima kasih', 'thanks', 'makasih', 'thx'], answer: 'Sama-sama! 🙏 Jika ada pertanyaan lain, jangan ragu untuk bertanya ya!' }
        ],
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.unread = 0;
                this.$nextTick(() => {
                    this.scrollToBottom();
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
            }
        },
        sendQuickQuestion(q) {
            this.userInput = q;
            this.sendMessage();
        },
        sendMessage() {
            const input = this.userInput.trim();
            if (!input) return;
            this.messages.push({ from: 'user', text: input });
            this.userInput = '';
            this.isTyping = true;
            this.scrollToBottom();

            setTimeout(() => {
                const lowerInput = input.toLowerCase();
                let reply = null;
                for (const faqItem of this.faq) {
                    if (faqItem.keywords.some(k => lowerInput.includes(k))) {
                        reply = faqItem.answer;
                        break;
                    }
                }
                if (!reply) {
                    reply = 'Maaf, saya belum bisa menjawab pertanyaan tersebut. 🤔<br>Silakan hubungi CS kami via <a href="https://wa.me/6281234567890" target="_blank" class="text-indigo-600 font-bold underline">WhatsApp</a> untuk bantuan lebih lanjut.';
                }
                this.isTyping = false;
                this.messages.push({ from: 'bot', text: reply });
                this.$nextTick(() => {
                    this.scrollToBottom();
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
            }, 800 + Math.random() * 600);
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.chatMessages;
                if (container) container.scrollTop = container.scrollHeight;
            });
        }
    }
}
</script>

</body>
</html>
