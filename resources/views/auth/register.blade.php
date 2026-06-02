@extends('layouts.app')

@section('title', 'Daftar Akun - MusicStore Luxe')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-[32px] p-8 md:p-10 shadow-[0_30px_80px_rgba(15,23,42,0.06)] space-y-8">
        <!-- Header -->
        <div class="text-center space-y-3">
            <span class="text-xs uppercase tracking-[0.4em] text-slate-500 font-bold">Gabung Sekarang</span>
            <h2 class="text-3xl font-black uppercase tracking-[-0.04em] text-slate-950">Daftar Akun</h2>
            <p class="text-sm text-slate-500">Mulai berbelanja instrumen musik dan vinyl impian Anda.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf
            
            <!-- Name -->
            <div class="space-y-2">
                <label for="name" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm @error('name') border-rose-500 @enderror" 
                    placeholder="John Doe" />
                @error('name')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="space-y-2">
                <label for="email" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm @error('email') border-rose-500 @enderror" 
                    placeholder="nama@email.com" />
                @error('email')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phone -->
            <div class="space-y-2">
                <label for="phone" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Nomor Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm @error('phone') border-rose-500 @enderror" 
                    placeholder="081234567890" />
                @error('phone')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label for="password" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm @error('password') border-rose-500 @enderror" 
                    placeholder="••••••••" />
                @error('password')
                    <span class="text-xs text-rose-600 font-medium block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="space-y-2">
                <label for="password_confirmation" class="text-xs uppercase tracking-widest text-slate-500 font-bold block">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-950 focus:bg-white transition text-sm" 
                    placeholder="••••••••" />
            </div>

            <!-- Submit -->
            <button type="submit" 
                class="w-full py-4 bg-slate-950 text-white rounded-2xl font-black uppercase text-xs tracking-[0.25em] hover:bg-slate-800 transition mt-2">
                Daftar Akun
            </button>
        </form>

        <!-- Footer -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-600">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="font-black text-slate-950 hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
