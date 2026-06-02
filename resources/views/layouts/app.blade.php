<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'MusicStore Luxe')</title>
    <!-- Google Fonts: Instrument Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Instrument Sans"', 'sans-serif'],
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
        <header class="bg-white border-b border-slate-100 sticky top-0 z-50">
            <div class="max-w-[1440px] mx-auto px-6 lg:px-10 py-5 flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 uppercase text-sm tracking-[0.55em] text-slate-900">
                    <span class="font-black text-lg">MUSICSTORE</span>
                    <span class="text-slate-500 text-[0.75rem] tracking-[0.7em]">LUXE</span>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-[0.82rem] uppercase tracking-[0.28em] text-slate-600">
                    <a href="{{ route('home') }}" class="hover:text-slate-900 {{ request()->routeIs('home') ? 'text-slate-900 font-bold' : '' }}">Home</a>
                    <a href="{{ route('catalog') }}" class="hover:text-slate-900 {{ request()->routeIs('catalog') ? 'text-slate-900 font-bold' : '' }}">Shop</a>
                </nav>

                <!-- Auth & Cart -->
                <div class="flex items-center gap-6">
                    @auth
                        <!-- Cart -->
                        <a href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            @php
                                $cartCount = \App\Models\CartItem::whereHas('cart', function($q) {
                                    $q->where('user_id', auth()->id());
                                })->sum('quantity');
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-1.5 -right-2 bg-slate-950 text-white text-[0.65rem] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center border border-white">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Orders -->
                        <a href="{{ route('orders.history') }}" class="text-slate-600 hover:text-slate-900 transition" title="Riwayat Pesanan">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-1.242-1.008-2.25-2.25-2.25H9m1.5-4.5h3m-9 13.5h12a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                            </svg>
                        </a>

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="text-xs uppercase tracking-[0.2em] font-black border border-slate-950 px-3 py-1.5 rounded-md hover:bg-slate-950 hover:text-white transition">Admin</a>
                        @endif

                        <!-- User Profile Info / Logout -->
                        <div class="flex items-center gap-4">
                            <span class="text-xs font-semibold text-slate-700 hidden sm:inline">Hi, {{ explode(' ', auth()->user()->name)[0] }}</span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-slate-400 hover:text-slate-950 text-xs tracking-widest uppercase transition">Keluar</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-900 text-xs tracking-[0.25em] uppercase">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 border border-slate-950 text-slate-950 text-xs tracking-[0.2em] hover:bg-slate-950 hover:text-white transition">Daftar</a>
                    @endauth
                </div>
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
                        <div><a href="{{ route('catalog', ['category' => 'guitars']) }}" class="hover:text-slate-900">Gitar</a></div>
                        <div><a href="{{ route('catalog', ['category' => 'vinyl-records']) }}" class="hover:text-slate-900">Vinyl</a></div>
                        <div><a href="{{ route('catalog', ['category' => 'audio-gear']) }}" class="hover:text-slate-900">Audio Gear</a></div>
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
</body>
</html>
