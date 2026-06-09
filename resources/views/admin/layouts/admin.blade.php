@extends('layouts.app')

@section('content')
<div x-data="{ mobileMenuOpen: false }" class="py-12 space-y-8 max-w-[1440px] mx-auto px-6 lg:px-10">
    <!-- Mobile Navigation Header -->
    <div class="lg:hidden flex items-center justify-between bg-cream-50 border-b border-walnut-800/10 pb-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 border border-walnut-800/20 text-walnut-900 flex items-center justify-center text-xs font-black uppercase">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div>
                <span class="text-[0.6rem] text-gold-600 uppercase tracking-[0.2em] font-bold block">Menu Admin ({{ strtoupper(auth()->user()->role) }})</span>
                <h4 class="text-sm font-display font-black text-walnut-950 uppercase tracking-tight truncate max-w-[180px]">{{ auth()->user()->name }}</h4>
            </div>
        </div>
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center gap-2 text-[0.65rem] border-b border-walnut-800/20 text-walnut-900 font-bold uppercase tracking-[0.2em] hover:border-gold-500 hover:text-gold-600 transition pb-1">
            <i data-lucide="menu" class="w-4 h-4"></i> Menu
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-12">
        <!-- Sidebar Navigation -->
        <div :class="mobileMenuOpen ? 'block' : 'hidden lg:block'" class="space-y-8 border-r border-walnut-800/10 pr-6">
            <div class="hidden lg:block pb-6 border-b border-walnut-800/10 mb-8">
                <span class="text-[0.65rem] text-gold-600 uppercase tracking-[0.3em] font-bold block mb-1">Panel Kendali</span>
                <h4 class="text-xl font-display font-black text-walnut-950 uppercase tracking-tight truncate">{{ auth()->user()->name }}</h4>
                <p class="text-[0.65rem] text-muted uppercase tracking-widest mt-1">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
            </div>
            
            <nav class="space-y-8">
                <!-- Dashboard -->
                <div>
                    <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.dashboard') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i> Dashboard
                    </a>
                </div>

                <!-- Products (Both Admin & SuperAdmin) -->
                <div class="space-y-3">
                    <span class="text-[0.6rem] uppercase tracking-[0.4em] text-walnut-400 font-bold block pl-3">Katalog</span>
                    <div class="flex flex-col gap-1">
                        <a href="{{ route('admin.products') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.products*') && !request()->routeIs('admin.products.trashed') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                            <i data-lucide="box" class="w-4 h-4 shrink-0"></i> Produk
                        </a>
                        <a href="{{ route('admin.products.trashed') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.products.trashed') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                            <i data-lucide="trash-2" class="w-4 h-4 shrink-0"></i> Sampah Produk
                        </a>
                        <a href="{{ route('admin.categories') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.categories*') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                            <i data-lucide="layers" class="w-4 h-4 shrink-0"></i> Kategori
                        </a>
                        <a href="{{ route('admin.brands') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.brands*') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                            <i data-lucide="award" class="w-4 h-4 shrink-0"></i> Merek
                        </a>
                    </div>
                </div>

                <!-- Transaksi (Both Admin & SuperAdmin) -->
                <div class="space-y-3">
                    <span class="text-[0.6rem] uppercase tracking-[0.4em] text-walnut-400 font-bold block pl-3">Transaksi</span>
                    <div class="flex flex-col gap-1">
                        <a href="{{ route('admin.orders') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.orders*') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                            <i data-lucide="shopping-cart" class="w-4 h-4 shrink-0"></i> Pesanan
                        </a>
                        <a href="{{ route('admin.returns') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.returns*') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                            <i data-lucide="refresh-cw" class="w-4 h-4 shrink-0"></i> Retur Barang
                        </a>
                    </div>
                </div>

                <!-- Promosi (Super Admin Only) -->
                @if(auth()->user()->role === 'super_admin')
                    <div class="space-y-3">
                        <span class="text-[0.6rem] uppercase tracking-[0.4em] text-walnut-400 font-bold block pl-3">Promosi</span>
                        <div class="flex flex-col gap-1">
                            <a href="{{ route('admin.coupons') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.coupons*') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                                <i data-lucide="ticket" class="w-4 h-4 shrink-0"></i> Kupon
                            </a>
                            <a href="{{ route('admin.flashSales') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.flashSales*') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                                <i data-lucide="zap" class="w-4 h-4 shrink-0"></i> Flash Sale
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Interaksi (Users: Super Admin only, Reviews: Both) -->
                <div class="space-y-3">
                    <span class="text-[0.6rem] uppercase tracking-[0.4em] text-walnut-400 font-bold block pl-3">Interaksi</span>
                    <div class="flex flex-col gap-1">
                        @if(auth()->user()->role === 'super_admin')
                            <a href="{{ route('admin.users') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.users*') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                                <i data-lucide="users" class="w-4 h-4 shrink-0"></i> Pengguna
                            </a>
                        @endif
                        <a href="{{ route('admin.reviews') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.reviews*') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                            <i data-lucide="message-square" class="w-4 h-4 shrink-0"></i> Ulasan
                        </a>
                    </div>
                </div>

                <!-- Laporan & Audit (Super Admin Only) -->
                @if(auth()->user()->role === 'super_admin')
                    <div class="space-y-3">
                        <span class="text-[0.6rem] uppercase tracking-[0.4em] text-walnut-400 font-bold block pl-3">Laporan & Audit</span>
                        <div class="flex flex-col gap-1">
                            <a href="{{ route('admin.reports.sales') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.reports.sales') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                                <i data-lucide="bar-chart-3" class="w-4 h-4 shrink-0"></i> Lap. Penjualan
                            </a>
                            <a href="{{ route('admin.reports.products') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.reports.products') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                                <i data-lucide="box" class="w-4 h-4 shrink-0"></i> Lap. Produk
                            </a>
                            <a href="{{ route('admin.reports.customers') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.reports.customers') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                                <i data-lucide="user-check" class="w-4 h-4 shrink-0"></i> Lap. Pelanggan
                            </a>
                            <a href="{{ route('admin.activityLog') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 py-2 text-[0.7rem] uppercase tracking-widest font-bold transition {{ request()->routeIs('admin.activityLog') ? 'text-gold-600 border-l-2 border-gold-500 pl-3' : 'text-walnut-800 hover:text-gold-600 border-l-2 border-transparent pl-3' }}">
                                <i data-lucide="history" class="w-4 h-4 shrink-0"></i> Audit Log
                            </a>
                        </div>
                    </div>
                @endif
            </nav>
        </div>

        <!-- Main Admin Content Area -->
        <div class="space-y-12">
            @yield('admin_content')
        </div>
    </div>
</div>
@endsection
