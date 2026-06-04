@extends('layouts.app')

@section('title', 'Daftar Akun - DjudasMS')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-[36px] p-8 md:p-10 shadow-sm space-y-8">
        <!-- Header -->
        <div class="text-center space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.4em] text-indigo-600 font-bold bg-indigo-50 px-3.5 py-1.5 rounded-full inline-block">Gabung Bersama Kami</span>
            <h2 class="font-display text-3xl font-black uppercase tracking-tight text-slate-950">Daftar Akun</h2>
            <p class="text-xs text-slate-500 font-normal">Mulai buat akun Anda untuk berbelanja instrumen musik dan vinyl impian.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4.5">
            @csrf
            
            <!-- Name -->
            <div class="space-y-1.5">
                <label for="name" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Nama Lengkap</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-slate-400">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                        class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('name') border-rose-500 @enderror" 
                        placeholder="John Doe" />
                </div>
                @error('name')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Alamat Email</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-slate-400">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('email') border-rose-500 @enderror" 
                        placeholder="nama@email.com" />
                </div>
                @error('email')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phone -->
            <div class="space-y-1.5">
                <label for="phone" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Nomor Telepon</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-slate-400">
                        <i data-lucide="phone" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                        class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('phone') border-rose-500 @enderror" 
                        placeholder="081234567890" />
                </div>
                @error('phone')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Password</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('password') border-rose-500 @enderror" 
                        placeholder="••••••••" />
                </div>
                @error('password')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Konfirmasi Password</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-slate-400">
                        <i data-lucide="lock-keyhole" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold" 
                        placeholder="••••••••" />
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" 
                class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 mt-2">
                Daftar Akun
            </button>
        </form>

        <!-- Footer -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-500 font-semibold">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-700 transition">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
