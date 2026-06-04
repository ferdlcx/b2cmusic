@extends('layouts.app')

@section('title', 'Masuk - MusicStore Luxe')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-[36px] p-8 md:p-10 shadow-sm space-y-8">
        <!-- Header -->
        <div class="text-center space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.4em] text-indigo-600 font-bold bg-indigo-50 px-3.5 py-1.5 rounded-full inline-block">Selamat Datang Kembali</span>
            <h2 class="font-display text-3xl font-black uppercase tracking-tight text-slate-950">Masuk Akun</h2>
            <p class="text-xs text-slate-500 font-normal">Gunakan email & password terdaftar Anda untuk berbelanja instrumen musik premium.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            
            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Alamat Email</label>
                <div class="relative">
                    <span class="absolute left-4 top-4 text-slate-400">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-11 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-sm font-medium @error('email') border-rose-500 @enderror" 
                        placeholder="nama@email.com" />
                </div>
                @error('email')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <div class="flex justify-between items-center">
                    <label for="password" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Password</label>
                </div>
                <div class="relative">
                    <span class="absolute left-4 top-4 text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-11 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-sm font-medium" 
                        placeholder="••••••••" />
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer" />
                <label for="remember" class="ml-2.5 text-sm text-slate-500 font-medium select-none cursor-pointer">Ingat saya di perangkat ini</label>
            </div>

            <!-- Submit -->
            <button type="submit" 
                class="w-full py-3.5 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-sm tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i> Masuk
            </button>
        </form>

        <!-- Seeder info helper for easy student demonstration -->
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-sm space-y-2 text-slate-500">
            <span class="font-bold text-slate-700 block uppercase tracking-wider text-[0.65rem]">Akun Demo Pengujian:</span>
            <div class="space-y-1 text-sm font-medium text-slate-600">
                <div class="flex justify-between">
                    <span>Pelanggan: <strong class="text-indigo-600">user@musicstore.com</strong></span>
                    <span>Pass: <strong class="text-slate-800">password</strong></span>
                </div>
                <div class="flex justify-between">
                    <span>Admin Toko: <strong class="text-indigo-600">admin@musicstore.com</strong></span>
                    <span>Pass: <strong class="text-slate-800">password</strong></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-500 font-semibold">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-700 transition">Daftar Sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection
