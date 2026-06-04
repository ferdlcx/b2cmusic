@extends('layouts.app')

@section('title', 'Reset Password - MusicStore Luxe')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-[36px] p-8 md:p-10 shadow-sm space-y-8">
        <!-- Header -->
        <div class="text-center space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.4em] text-indigo-600 font-bold bg-indigo-50 px-3.5 py-1.5 rounded-full inline-block">Atur Ulang</span>
            <h2 class="font-display text-3xl font-black uppercase tracking-tight text-slate-950">Reset Password</h2>
            <p class="text-xs text-slate-500 font-normal">Buat password baru untuk akun Anda. Pastikan menggunakan kombinasi yang kuat dan mudah diingat.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('password.update') }}" method="POST" class="space-y-4.5">
            @csrf

            <!-- Token -->
            <input type="hidden" name="token" value="{{ $token ?? request()->route('token') }}" />

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Alamat Email</label>
                <div class="relative">
                    <span class="absolute left-4 top-4.5 text-slate-400">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email', $email ?? request()->email) }}" required autofocus
                        class="w-full pl-11 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('email') border-rose-500 @enderror" 
                        placeholder="nama@email.com" />
                </div>
                @error('email')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- New Password -->
            <div class="space-y-1.5">
                <label for="password" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Password Baru</label>
                <div class="relative">
                    <span class="absolute left-4 top-4.5 text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-11 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('password') border-rose-500 @enderror" 
                        placeholder="••••••••" />
                </div>
                @error('password')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Konfirmasi Password Baru</label>
                <div class="relative">
                    <span class="absolute left-4 top-4.5 text-slate-400">
                        <i data-lucide="lock-keyhole" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full pl-11 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold" 
                        placeholder="••••••••" />
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" 
                class="w-full py-4.5 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2 mt-2">
                <i data-lucide="shield-check" class="w-4 h-4"></i> Reset Password
            </button>
        </form>

        <!-- Footer -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-500 font-semibold">
                Kembali ke halaman 
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-700 transition">Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection
