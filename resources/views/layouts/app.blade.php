<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'DjudasMS')</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://i.ibb.co/Fv34y3h/jms.png" />
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

        /* Smooth scrollbar for webkit */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--color-cream-100);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--color-gold-400);
            border-radius: 100px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-gold-500);
        }

        /* Hide number input spin buttons */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
        }

        /* Mobile Responsive Table Scrolling */
        @media (max-width: 1024px) {
            div.overflow-hidden:has(table) {
                overflow-x: auto !important;
            }
        }
    </style>
</head>
<body class="font-sans text-walnut-900 bg-cream-100 min-h-screen flex flex-col justify-between overflow-x-hidden">

    <div>
        <!-- Navbar -->
        <header x-data="{ mobileMenuOpen: false }" class="bg-cream-100/90 backdrop-blur-md border-b border-walnut-800/10 sticky top-0 z-50 transition duration-300">
            <div class="max-w-[1440px] mx-auto px-6 lg:px-10 py-5 flex items-center justify-between">
                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl text-walnut-800 hover:bg-cream-200 transition">
                    <template x-if="!mobileMenuOpen">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </template>
                    <template x-if="mobileMenuOpen">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </template>
                </button>

                <!-- Logo -->
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-walnut-950 hover:opacity-80 transition">
                    <span class="font-display font-black text-xl tracking-tight uppercase flex items-center gap-1.5">
                        <i data-lucide="disc" class="w-6 h-6 text-gold-500"></i>
                        DjudasMS <span class="text-gold-500 font-semibold text-[0.7rem] tracking-[0.25em] self-end mb-0.5 hidden sm:inline-block">LUXE</span>
                    </span>
                </a>

                <!-- Navigation Links (Desktop) -->
                <nav class="hidden md:flex items-center gap-10 text-[0.8rem] font-semibold tracking-[0.1em] text-walnut-800 uppercase">
                    <a href="{{ route('home') }}" class="hover:text-gold-600 transition flex items-center gap-1.5 {{ request()->routeIs('home') ? 'text-gold-600' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('catalog') }}" class="hover:text-gold-600 transition flex items-center gap-1.5 {{ request()->routeIs('catalog') ? 'text-gold-600' : '' }}">
                        Shop Catalog
                    </a>
                    <!-- Dev Dropdown -->
                    <div x-data="{ open: false }" class="relative" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="hover:text-gold-600 transition flex items-center gap-1.5 {{ request()->routeIs('docs') || request()->routeIs('doctest') ? 'text-gold-600' : '' }}">
                            Dev <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute top-full mt-2 w-48 bg-white border border-walnut-800/10 shadow-xl py-2 z-50 rounded-xl"
                             style="display: none;">
                            <a href="{{ route('docs') }}" class="flex items-center gap-3 px-5 py-2.5 text-xs font-semibold text-walnut-800 hover:bg-cream-100 hover:text-gold-600 transition">
                                <i data-lucide="book-open" class="w-4 h-4"></i> Docs
                            </a>
                            <a href="{{ route('doctest') }}" class="flex items-center gap-3 px-5 py-2.5 text-xs font-semibold text-walnut-800 hover:bg-cream-100 hover:text-gold-600 transition">
                                <i data-lucide="flask-conical" class="w-4 h-4"></i> DocTest
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Auth & Cart Actions -->
                <div class="flex items-center gap-5">
                    @auth
                        <!-- Wishlist -->
                        <a href="{{ route('wishlist.index') }}" class="hidden md:inline-flex relative text-walnut-800 hover:text-gold-600 transition" title="Wishlist Saya">
                            <i data-lucide="heart" class="w-5 h-5"></i>
                        </a>

                        <!-- Cart -->
                        <div x-data="{ 
                            cartCount: {{ \App\Models\CartItem::whereHas('cart', function($q) { $q->where('user_id', auth()->id()); })->sum('quantity') ?: 0 }},
                            animate: false,
                            init() {
                                document.addEventListener('cart-updated', (e) => {
                                    this.cartCount = e.detail.count;
                                    this.animate = true;
                                    setTimeout(() => this.animate = false, 300);
                                });
                            }
                        }">
                            <a href="{{ route('cart.index') }}" class="relative text-walnut-800 hover:text-gold-600 transition block" title="Keranjang Belanja">
                                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                                <template x-if="cartCount > 0">
                                    <span :class="animate ? 'animate-pop' : ''" class="absolute -top-1.5 -right-1.5 bg-gold-500 text-white text-[0.55rem] font-bold px-1 py-0.5 rounded-full min-w-[16px] text-center" x-text="cartCount"></span>
                                </template>
                            </a>
                        </div>

                        <!-- Orders (Desktop Only) -->
                        <a href="{{ route('orders.history') }}" class="hidden md:inline-flex text-walnut-800 hover:text-gold-600 transition" title="Riwayat Pesanan">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </a>

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                            <a href="{{ route('admin.dashboard') }}" class="hidden md:inline-flex text-[0.65rem] font-bold tracking-[0.2em] border border-walnut-800 text-walnut-900 px-3 py-1.5 uppercase hover:bg-walnut-900 hover:text-white transition">Admin</a>
                        @endif

                        <!-- User Profile Dropdown (Desktop Only) -->
                        <div x-data="{ open: false }" @click.away="open = false" class="relative hidden md:block">
                            <button @click="open = !open" class="flex items-center gap-2 focus:outline-none hover:text-gold-600 transition">
                                <div class="w-7 h-7 border border-walnut-800 text-walnut-800 flex items-center justify-center rounded-full text-[0.6rem] font-bold uppercase">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="text-[0.75rem] font-bold tracking-widest text-walnut-800 uppercase hidden lg:inline">{{ explode(' ', auth()->user()->name)[0] }}</span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-walnut-800"></i>
                            </button>
                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="opacity-0 scale-95" 
                                 x-transition:enter-end="opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-75" 
                                 x-transition:leave-start="opacity-100 scale-100" 
                                 x-transition:leave-end="opacity-0 scale-95" 
                                 class="absolute right-0 mt-3 w-56 bg-cream-50 border border-walnut-800/10 py-2 z-50"
                                 style="display: none;">
                                <div class="px-5 py-3 border-b border-walnut-800/10">
                                    <p class="text-[0.6rem] text-muted uppercase tracking-[0.2em] font-bold">Pengguna</p>
                                    <p class="text-xs font-semibold text-walnut-950 truncate mt-1">{{ auth()->user()->email }}</p>
                                </div>
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                                    <a href="{{ route('admin.dashboard') }}" class="flex md:hidden items-center gap-3 px-5 py-2.5 text-sm font-medium text-gold-600 hover:bg-cream-100">
                                        <i data-lucide="shield" class="w-4 h-4"></i> Panel Admin
                                    </a>
                                @endif
                                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm font-medium text-walnut-800 hover:bg-cream-100">
                                    <i data-lucide="user" class="w-4 h-4"></i> Profil Saya
                                </a>
                                <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm font-medium text-walnut-800 hover:bg-cream-100">
                                    <i data-lucide="heart" class="w-4 h-4"></i> Wishlist Saya
                                </a>
                                <a href="{{ route('orders.history') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm font-medium text-walnut-800 hover:bg-cream-100">
                                    <i data-lucide="package" class="w-4 h-4"></i> Pesanan Saya
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-3 px-5 py-2.5 text-sm font-medium text-red-700 hover:bg-red-50 text-left border-t border-walnut-800/5">
                                        <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden md:inline-flex text-walnut-800 hover:text-gold-600 text-[0.7rem] font-bold tracking-[0.1em] uppercase transition">Masuk</a>
                        <a href="{{ route('register') }}" class="hidden md:inline-flex items-center justify-center px-5 py-2.5 bg-walnut-900 text-cream-50 text-[0.7rem] font-bold tracking-[0.1em] uppercase hover:bg-gold-600 transition duration-300">Daftar</a>
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
                 class="md:hidden bg-cream-50 border-b border-walnut-800/10 px-6 py-4 space-y-4 shadow-sm"
                 style="display: none;">
                <a href="{{ route('home') }}" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-[0.1em] font-bold text-walnut-800 hover:text-gold-600 transition">
                    <i data-lucide="home" class="w-4 h-4"></i> Home
                </a>
                <a href="{{ route('catalog') }}" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-[0.1em] font-bold text-walnut-800 hover:text-gold-600 transition">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i> Shop Catalog
                </a>
                <div class="pt-2">
                    <p class="text-[0.6rem] font-bold text-walnut-400 uppercase tracking-widest px-2 mb-2">Developer</p>
                    <a href="{{ route('docs') }}" class="flex items-center gap-3 py-2 px-2 text-[0.7rem] uppercase tracking-[0.1em] font-bold text-walnut-800 hover:text-gold-600 transition">
                        <i data-lucide="book-open" class="w-4 h-4"></i> Docs
                    </a>
                    <a href="{{ route('doctest') }}" class="flex items-center gap-3 py-2 px-2 text-[0.7rem] uppercase tracking-[0.1em] font-bold text-walnut-800 hover:text-gold-600 transition">
                        <i data-lucide="flask-conical" class="w-4 h-4"></i> DocTest
                    </a>
                </div>
                @auth
                    <hr class="border-walnut-800/10 my-2" />
                    <a href="{{ route('orders.history') }}" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-[0.1em] font-bold text-walnut-800 hover:text-gold-600 transition">
                        <i data-lucide="clipboard-list" class="w-4 h-4"></i> Riwayat Pesanan
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-[0.1em] font-bold text-walnut-800 hover:text-gold-600 transition">
                        <i data-lucide="heart" class="w-4 h-4"></i> Wishlist Saya
                    </a>
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-[0.1em] font-bold text-walnut-800 hover:text-gold-600 transition">
                        <i data-lucide="user" class="w-4 h-4"></i> Profil Saya
                    </a>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-[0.1em] font-bold text-walnut-900 hover:text-gold-600 transition">
                            <i data-lucide="shield-check" class="w-4 h-4"></i> Panel Admin
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="w-full pt-2">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 py-2 text-[0.7rem] uppercase tracking-[0.1em] font-bold text-red-700 hover:text-red-800 transition text-left">
                            <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                        </button>
                    </form>
                @else
                    <hr class="border-walnut-800/10 my-2" />
                    <a href="{{ route('login') }}" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-[0.1em] font-bold text-walnut-800 hover:text-gold-600 transition">
                        <i data-lucide="log-in" class="w-4 h-4"></i> Masuk
                    </a>
                    <a href="{{ route('register') }}" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-[0.1em] font-bold text-walnut-800 hover:text-gold-600 transition">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Daftar
                    </a>
                @endauth
            </div>
        </header>

        <!-- Modern Toast Notifications -->
        <div class="fixed bottom-6 right-6 z-[110] flex flex-col gap-3 pointer-events-none" id="toast-container">
            @if(session('success'))
                <div class="toast-message pointer-events-auto flex items-center justify-between gap-4 bg-walnut-950 text-cream-50 px-5 py-3.5 shadow-xl border border-walnut-800/10 min-w-[300px] animate-fade-in-up">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
                        <span class="text-[0.75rem] font-medium tracking-wide">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.style.opacity='0'; setTimeout(()=>this.parentElement.remove(), 300)" class="text-walnut-400 hover:text-white transition">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="toast-message pointer-events-auto flex items-center justify-between gap-4 bg-walnut-950 text-cream-50 px-5 py-3.5 shadow-xl border border-walnut-800/10 min-w-[300px] animate-fade-in-up">
                    <div class="flex items-center gap-3">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400"></i>
                        <span class="text-[0.75rem] font-medium tracking-wide">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.style.opacity='0'; setTimeout(()=>this.parentElement.remove(), 300)" class="text-walnut-400 hover:text-white transition">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="toast-message pointer-events-auto flex flex-col gap-2 bg-walnut-950 text-cream-50 px-5 py-4 shadow-xl border border-walnut-800/10 min-w-[300px] max-w-[400px] animate-fade-in-up">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i>
                            <span class="text-[0.75rem] font-bold tracking-widest uppercase">Validasi Gagal</span>
                        </div>
                        <button onclick="this.parentElement.parentElement.style.opacity='0'; setTimeout(()=>this.parentElement.parentElement.remove(), 300)" class="text-walnut-400 hover:text-white transition">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                    <ul class="ml-7 text-[0.7rem] text-walnut-300 font-medium list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        
        <script>
            // Auto-hide toasts after 5 seconds
            setTimeout(() => {
                document.querySelectorAll('.toast-message').forEach(toast => {
                    toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(10px)';
                    setTimeout(() => toast.remove(), 300);
                });
            }, 5000);
        </script>
                        <!-- Content -->
        <main class="max-w-[1440px] mx-auto px-6 lg:px-10 py-8">
            @yield('content')
        </main>
        
        <!-- Script for Cart Form -->
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('cartForm', () => ({
                    loading: false,
                    qty: 1,
                    submitForm(e, actionType = 'add_to_cart') {
                        if (actionType === 'buy_now') return; // Let it submit normally to redirect
                        
                        e.preventDefault();
                        this.loading = true;
                        
                        const form = e.target;
                        const formData = new FormData(form);
                        if (!formData.has('action')) {
                            formData.append('action', 'add_to_cart');
                        }
                        
                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (response.ok || response.redirected) {
                                const cartElem = document.querySelector('[x-data*=cartCount]');
                                if (cartElem && cartElem.__x) {
                                    const currentCount = cartElem.__x.$data.cartCount;
                                    const addedQty = parseInt(formData.get('quantity')) || 1;
                                    
                                    document.dispatchEvent(new CustomEvent('cart-updated', { 
                                        detail: { count: currentCount + addedQty } 
                                    }));
                                    
                                    this.showToast('Produk ditambahkan ke keranjang!');
                                }
                            }
                        })
                        .catch(err => console.error(err))
                        .finally(() => {
                            this.loading = false;
                        });
                    },
                    showToast(msg) {
                        const container = document.getElementById('toast-container');
                        if (!container) return;
                        const toast = document.createElement('div');
                        toast.className = 'toast-message pointer-events-auto flex items-center justify-between gap-4 bg-walnut-950 text-cream-50 px-5 py-3.5 shadow-xl border border-walnut-800/10 min-w-[300px] animate-fade-in-up';
                        toast.innerHTML = `
                            <div class="flex items-center gap-3">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
                                <span class="text-[0.75rem] font-medium tracking-wide">${msg}</span>
                            </div>
                            <button onclick="this.parentElement.style.opacity='0'; setTimeout(()=>this.parentElement.remove(), 300)" class="text-walnut-400 hover:text-white transition">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        `;
                        container.prepend(toast);
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                        setTimeout(() => {
                            toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            toast.style.opacity = '0';
                            toast.style.transform = 'translateY(10px)';
                            setTimeout(() => toast.remove(), 300);
                        }, 5000);
                    }
                }));
            });
        </script>
    </div>

    <!-- Footer -->
    <footer class="bg-cream-50 border-t border-walnut-800/10 py-20 text-walnut-800 mt-20">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-16 pb-16 border-b border-walnut-800/10">
                <div class="space-y-6">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3 uppercase text-sm tracking-[0.5em] text-walnut-950">
                        <span class="font-black text-xl">DJUDASMS</span>
                        <span class="text-gold-600 text-[0.65rem] font-bold tracking-[0.6em] self-center">LUXE</span>
                    </a>
                    <p class="text-[0.8rem] text-muted max-w-sm leading-relaxed">
                        Destinasi belanja instrumen musik premium dan perlengkapan studio dengan kualitas desain editorial murni.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-8 text-[0.7rem] uppercase tracking-[0.2em] font-bold">
                    <div class="space-y-5">
                        <div class="text-walnut-950 opacity-50">Kategori</div>
                        <div><a href="{{ route('catalog', ['category' => 'gitar-akustik']) }}" class="hover:text-gold-600 transition">Gitar</a></div>
                        <div><a href="{{ route('catalog', ['category' => 'keyboard-piano']) }}" class="hover:text-gold-600 transition">Keyboard & Piano</a></div>
                        <div><a href="{{ route('catalog', ['category' => 'audio-recording']) }}" class="hover:text-gold-600 transition">Audio Gear</a></div>
                    </div>
                    <div class="space-y-5">
                        <div class="text-walnut-950 opacity-50">Eksplorasi</div>
                        <div><a href="{{ route('catalog') }}" class="hover:text-gold-600 transition">Semua Produk</a></div>
                        <div><a href="{{ route('about') }}" class="hover:text-gold-600 transition">Tentang Kami</a></div>
                        <div><a href="{{ route('contact') }}" class="hover:text-gold-600 transition">Kontak</a></div>
                    </div>
                </div>
                <div class="space-y-5 text-[0.7rem] uppercase tracking-[0.2em] font-bold">
                    <div class="text-walnut-950 opacity-50">Ikuti Kami</div>
                    <div class="flex flex-col gap-4">
                        <a href="#" class="hover:text-gold-600 transition">Instagram</a>
                        <a href="#" class="hover:text-gold-600 transition">YouTube</a>
                        <a href="#" class="hover:text-gold-600 transition">Pinterest</a>
                    </div>
                </div>
            </div>
            <div class="pt-10 flex flex-col md:flex-row justify-between items-center gap-4 text-[0.65rem] text-muted tracking-[0.1em] uppercase font-semibold">
                <div>&copy; {{ date('Y') }} DjudasMS. B2C E-commerce.</div>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-walnut-900 transition">Privacy Policy</a>
                    <a href="#" class="hover:text-walnut-900 transition">Terms of Service</a>
                </div>
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
        class="absolute bottom-20 right-0 w-[360px] max-h-[520px] bg-cream-50 border border-walnut-800/10 shadow-2xl overflow-hidden flex flex-col">
        
        <!-- Header -->
        <div class="bg-walnut-950 p-5 text-gold-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 border border-gold-500/30 rounded-none flex items-center justify-center">
                        <i data-lucide="headphones" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm tracking-widest uppercase">DjudasMS Support</h4>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span class="text-[0.65rem] text-cream-50/70 font-bold tracking-widest uppercase">Online sekarang</span>
                        </div>
                    </div>
                </div>
                <button @click="isOpen = false" class="w-8 h-8 hover:bg-white/10 rounded-lg flex items-center justify-center transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 max-h-[300px]" x-ref="chatMessages">
            <template x-for="(msg, idx) in messages" :key="idx">
                <div :class="msg.from === 'bot' ? 'flex justify-start' : 'flex justify-end'">
                    <div :class="msg.from === 'bot' ? 'bg-cream-100 text-walnut-900 border border-walnut-800/10' : 'bg-walnut-900 text-cream-50'" class="px-4 py-3 max-w-[85%] text-[0.75rem] leading-relaxed font-medium">
                        <span x-html="msg.text"></span>
                    </div>
                </div>
            </template>
            <div x-show="isTyping" class="flex justify-start">
                <div class="bg-cream-100 text-walnut-500 px-4 py-3 flex items-center gap-1.5 border border-walnut-800/10">
                    <span class="w-1.5 h-1.5 bg-walnut-400 animate-bounce" style="animation-delay: 0s"></span>
                    <span class="w-1.5 h-1.5 bg-walnut-400 animate-bounce" style="animation-delay: 0.15s"></span>
                    <span class="w-1.5 h-1.5 bg-walnut-400 animate-bounce" style="animation-delay: 0.3s"></span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="px-4 pb-4" x-show="messages.length <= 1">
            <div class="flex flex-wrap gap-2">
                <button @click="sendQuickQuestion('Cara bayar?')" class="px-3 py-1.5 bg-cream-100 border border-walnut-800/10 text-walnut-900 text-[0.65rem] font-bold uppercase tracking-wider hover:bg-gold-500 hover:text-white transition">Cara bayar?</button>
                <button @click="sendQuickQuestion('Cara retur?')" class="px-3 py-1.5 bg-cream-100 border border-walnut-800/10 text-walnut-900 text-[0.65rem] font-bold uppercase tracking-wider hover:bg-gold-500 hover:text-white transition">Cara retur?</button>
                <button @click="sendQuickQuestion('Jam operasional?')" class="px-3 py-1.5 bg-cream-100 border border-walnut-800/10 text-walnut-900 text-[0.65rem] font-bold uppercase tracking-wider hover:bg-gold-500 hover:text-white transition">Jam operasional?</button>
                <button @click="sendQuickQuestion('Hubungi CS')" class="px-3 py-1.5 bg-walnut-900 text-gold-500 text-[0.65rem] font-bold uppercase tracking-wider hover:bg-gold-600 hover:text-white transition">Hubungi CS</button>
            </div>
        </div>

        <!-- Input -->
        <div class="p-3 border-t border-walnut-800/10">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input x-model="userInput" type="text" placeholder="Ketik pesan..." class="flex-1 px-4 py-2.5 bg-cream-100 border border-walnut-800/10 text-[0.75rem] font-medium focus:outline-none focus:border-gold-500 transition" />
                <button type="submit" class="w-10 h-10 bg-walnut-900 text-gold-500 flex items-center justify-center hover:bg-gold-600 hover:text-white transition shrink-0">
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Floating Button -->
    <button @click="toggleChat()" 
        class="w-14 h-14 bg-walnut-950 text-gold-500 rounded-none shadow-xl hover:bg-gold-500 hover:text-white transition-colors duration-300 flex items-center justify-center relative">
        <i data-lucide="message-square" class="w-5 h-5" x-show="!isOpen"></i>
        <i data-lucide="x" class="w-6 h-6" x-show="isOpen"></i>
        <span x-show="!isOpen && unread > 0" class="absolute -top-2 -right-2 w-5 h-5 bg-gold-600 text-white text-[0.6rem] font-bold rounded-full flex items-center justify-center animate-bounce" x-text="unread"></span>
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
                    reply = 'Maaf, saya belum bisa menjawab pertanyaan tersebut. 🤔<br>Silakan hubungi CS kami via <a href="https://wa.me/6281234567890" target="_blank" class="text-gold-600 font-bold underline">WhatsApp</a> untuk bantuan lebih lanjut.';
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
