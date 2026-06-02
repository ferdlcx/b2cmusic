<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'MusicStore Luxe')</title>
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
</head>
<body class="font-sans text-slate-900 bg-[#f8f8f8] min-h-screen flex flex-col justify-between overflow-x-hidden">
    <div>
        <!-- Navbar -->
        <header x-data="{ mobileMenuOpen: false }" class="bg-white/80 backdrop-blur-xl border-b border-slate-100/85 sticky top-0 z-50 transition duration-300">
            <div class="max-w-[1440px] mx-auto px-6 lg:px-10 py-4.5 flex items-center justify-between">
                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none transition">
                    <template x-if="!mobileMenuOpen">
                        <i data-lucide="menu" class="w-5.5 h-5.5"></i>
                    </template>
                    <template x-if="mobileMenuOpen">
                        <i data-lucide="x" class="w-5.5 h-5.5"></i>
                    </template>
                </button>

                <!-- Logo -->
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-slate-900 hover:opacity-90 transition">
                    <span class="font-display font-black text-xl tracking-tight uppercase flex items-center gap-1.5">
                        <i data-lucide="music-4" class="w-6 h-6 text-indigo-600"></i>
                        MusicStore <span class="text-indigo-600 font-semibold text-[0.7rem] tracking-[0.25em] self-end mb-0.5">LUXE</span>
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

                        <!-- Orders -->
                        <a href="{{ route('orders.history') }}" class="p-2.5 rounded-2xl hover:bg-slate-100 text-slate-600 hover:text-indigo-600 transition" title="Riwayat Pesanan">
                            <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                        </a>

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                            <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex text-xs font-semibold tracking-wider bg-indigo-50 border border-indigo-200 text-indigo-700 px-3 py-1.5 rounded-xl hover:bg-indigo-600 hover:text-white transition">Admin</a>
                        @endif

                        <!-- User Profile Dropdown -->
                        <div x-data="{ open: false }" @click.away="open = false" class="relative">
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
                        <a href="{{ route('login') }}" class="text-slate-600 hover:text-indigo-600 text-xs font-semibold tracking-wider uppercase transition">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4.5 py-2.5 bg-indigo-600 text-white rounded-2xl text-xs font-semibold tracking-wider hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300">Daftar</a>
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
                &copy; {{ date('Y') }} MusicStore Luxe. Dibuat untuk Tugas Kuliah. model B2C E-commerce.
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
