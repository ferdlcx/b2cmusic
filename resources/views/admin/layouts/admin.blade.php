@extends('layouts.app')

@section('content')
<div x-data="{ mobileMenuOpen: false }" class="py-4 space-y-6">
    <!-- Mobile Navigation Header -->
    <div class="lg:hidden flex items-center justify-between bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-indigo-50 text-indigo-700 flex items-center justify-center rounded-full text-xs font-black uppercase">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div>
                <span class="text-[0.6rem] text-slate-400 uppercase tracking-widest font-bold block">Menu Admin ({{ strtoupper(auth()->user()->role) }})</span>
                <h4 class="text-xs font-black text-slate-800 uppercase tracking-tight truncate max-w-[150px]">{{ auth()->user()->name }}</h4>
            </div>
        </div>
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center gap-1.5 text-xs bg-indigo-50 text-indigo-700 px-4 py-2.5 rounded-xl font-bold hover:bg-indigo-100 transition shadow-sm">
            <i data-lucide="menu" class="w-4 h-4"></i> Menu
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Navigation -->
        <div :class="mobileMenuOpen ? 'block' : 'hidden lg:block'" class="space-y-6">
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 shadow-sm">
                <div class="hidden lg:block px-3 py-2 border-b border-slate-100 mb-4">
                    <span class="text-[0.65rem] text-slate-400 uppercase tracking-widest font-bold">Menu Admin ({{ strtoupper(auth()->user()->role) }})</span>
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-tight truncate">{{ auth()->user()->name }}</h4>
                </div>
                
                <nav class="space-y-1.5 text-xs font-semibold text-slate-600">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i> Dashboard
                    </a>
    
                    <!-- Products (Both Admin & SuperAdmin) -->
                    <div>
                        <span class="px-3 pt-3 pb-1 text-[0.6rem] uppercase tracking-wider text-slate-400 font-bold block">Katalog</span>
                        <a href="{{ route('admin.products') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.products*') && !request()->routeIs('admin.products.trashed') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                            <i data-lucide="box" class="w-4 h-4 shrink-0"></i> Produk
                        </a>
                        <a href="{{ route('admin.products.trashed') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.products.trashed') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                            <i data-lucide="trash-2" class="w-4 h-4 shrink-0"></i> Sampah Produk
                        </a>
                        <a href="{{ route('admin.categories') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.categories*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                            <i data-lucide="layers" class="w-4 h-4 shrink-0"></i> Kategori
                        </a>
                        <a href="{{ route('admin.brands') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.brands*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                            <i data-lucide="award" class="w-4 h-4 shrink-0"></i> Brand
                        </a>
                    </div>
    
                    <!-- Transaksi (Both Admin & SuperAdmin) -->
                    <div>
                        <span class="px-3 pt-3 pb-1 text-[0.6rem] uppercase tracking-wider text-slate-400 font-bold block">Transaksi</span>
                        <a href="{{ route('admin.orders') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.orders*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                            <i data-lucide="shopping-cart" class="w-4 h-4 shrink-0"></i> Pesanan
                        </a>
                        <a href="{{ route('admin.returns') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.returns*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                            <i data-lucide="refresh-cw" class="w-4 h-4 shrink-0"></i> Retur Barang
                        </a>
                    </div>
    
                    <!-- Promosi (Super Admin Only) -->
                    @if(auth()->user()->role === 'super_admin')
                        <div>
                            <span class="px-3 pt-3 pb-1 text-[0.6rem] uppercase tracking-wider text-slate-400 font-bold block">Promosi</span>
                            <a href="{{ route('admin.coupons') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.coupons*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                                <i data-lucide="ticket" class="w-4 h-4 shrink-0"></i> Kupon
                            </a>
                            <a href="{{ route('admin.flashSales') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.flashSales*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                                <i data-lucide="zap" class="w-4 h-4 shrink-0"></i> Flash Sale
                            </a>
                        </div>
                    @endif
    
                    <!-- Interaksi (Users: Super Admin only, Reviews: Both) -->
                    <div>
                        <span class="px-3 pt-3 pb-1 text-[0.6rem] uppercase tracking-wider text-slate-400 font-bold block">Interaksi</span>
                        @if(auth()->user()->role === 'super_admin')
                            <a href="{{ route('admin.users') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                                <i data-lucide="users" class="w-4 h-4 shrink-0"></i> Pengguna
                            </a>
                        @endif
                        <a href="{{ route('admin.reviews') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reviews*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                            <i data-lucide="message-square" class="w-4 h-4 shrink-0"></i> Ulasan
                        </a>
                    </div>
    
                    <!-- Laporan & Audit (Super Admin Only) -->
                    @if(auth()->user()->role === 'super_admin')
                        <div>
                            <span class="px-3 pt-3 pb-1 text-[0.6rem] uppercase tracking-wider text-slate-400 font-bold block">Laporan & Audit</span>
                            <a href="{{ route('admin.reports.sales') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.sales') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                                <i data-lucide="bar-chart-3" class="w-4 h-4 shrink-0"></i> Lap. Penjualan
                            </a>
                            <a href="{{ route('admin.reports.products') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.products') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                                <i data-lucide="box" class="w-4 h-4 shrink-0"></i> Lap. Produk
                            </a>
                            <a href="{{ route('admin.reports.customers') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.customers') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                                <i data-lucide="user-check" class="w-4 h-4 shrink-0"></i> Lap. Pelanggan
                            </a>
                            <a href="{{ route('admin.activityLog') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.activityLog') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : 'hover:bg-slate-50 hover:text-slate-900' }}">
                                <i data-lucide="history" class="w-4 h-4 shrink-0"></i> Audit Log
                            </a>
                        </div>
                    @endif
                </nav>
            </div>
        </div>
    
        <!-- Main Admin Content Area -->
        <div class="lg:col-span-3 space-y-6">
            @yield('admin_content')
        </div>
    </div>
</div>
@endsection
