@extends('layouts.app')

@section('title', 'Lupa Password - MusicStore Luxe')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-[36px] p-8 md:p-10 shadow-sm space-y-8">
        <!-- Header -->
        <div class="text-center space-y-2">
            <span class="text-[0.65rem] uppercase tracking-[0.4em] text-indigo-600 font-bold bg-indigo-50 px-3.5 py-1.5 rounded-full inline-block">Reset Password</span>
            <h2 class="font-display text-3xl font-black uppercase tracking-tight text-slate-950">Lupa Password</h2>
            <p class="text-xs text-slate-500 font-normal">Masukkan alamat email Anda dan kami akan mengirimkan link untuk mengatur ulang password.</p>
        </div>

        <!-- Success Message -->
        @if (session('status'))
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-start gap-3">
                <span class="text-emerald-600 mt-0.5 shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </span>
                <p class="text-xs text-emerald-700 font-semibold">{{ session('status') }}</p>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Alamat Email</label>
                <div class="relative">
                    <span class="absolute left-4 top-4.5 text-slate-400">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-11 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition text-xs font-semibold @error('email') border-rose-500 @enderror" 
                        placeholder="nama@email.com" />
                </div>
                @error('email')
                    <span class="text-[0.65rem] text-rose-600 font-semibold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit -->
            <button type="submit" 
                class="w-full py-4.5 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2">
                <i data-lucide="send" class="w-4 h-4"></i> Kirim Link Reset Password
            </button>
        </form>

        <!-- Footer -->
        <div class="text-center pt-2">
            <p class="text-xs text-slate-500 font-semibold">
                Ingat password Anda? 
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-700 transition">Kembali ke Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection
