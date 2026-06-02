@extends('layouts.app')

@section('title', 'Masuk - MusicStore Luxe')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-[32px] p-8 md:p-10 shadow-[0_30px_80px_rgba(15,23,42,0.06)] space-y-8">
        <!-- Header -->
        <div class="text-center space-y-3">
            <span class="text-xs uppercase tracking-[0.4em] text-slate-500 font-bold">Selamat Datang</span>
            <h2 class="text-3xl font-black uppercase tracking-[-0.04em] text-slate-950">Masuk Akun</h2>
            <p class="text-sm text-slate-500">Gunakan akun Anda untuk berbelanja produk musik premium.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Email -->
            <div class="space-y-2">
                <label for="email" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm @error('email') border-rose-500 @enderror" 
                    placeholder="nama@email.com" />
                @error('email')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label for="password" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm" 
                    placeholder="••••••••" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-slate-950 border-slate-300 rounded focus:ring-slate-950" />
                <label for="remember" class="ml-2.5 text-xs text-slate-600 font-semibold select-none">Ingat saya di perangkat ini</label>
            </div>

            <!-- Submit -->
            <button type="submit" 
                class="w-full py-4 bg-slate-950 text-white rounded-2xl font-black uppercase text-xs tracking-[0.25em] hover:bg-slate-800 transition">
                Masuk
            </button>
        </form>

        <!-- Seeder info helper for easy student demonstration! -->
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs space-y-1.5 text-slate-500">
            <span class="font-bold text-slate-700 block uppercase tracking-wider text-[0.65rem]">Akun Demo Pengujian:</span>
            <div class="flex justify-between">
                <span>Customer: <strong class="text-slate-800">user@musicstore.com</strong></span>
                <span>Pass: <strong class="text-slate-800">password</strong></span>
            </div>
            <div class="flex justify-between">
                <span>Admin: <strong class="text-slate-800">admin@musicstore.com</strong></span>
                <span>Pass: <strong class="text-slate-800">password</strong></span>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-600">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-black text-slate-950 hover:underline">Daftar Sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection
