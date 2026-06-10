@extends('layouts.app')

@section('title', 'Lupa Password - DjudasMS')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-cream-50 p-10 border border-walnut-800/10 relative overflow-hidden">

        <div class="relative z-10 text-center space-y-2">
            <div class="mx-auto w-14 h-14 bg-walnut-900 text-gold-500 flex items-center justify-center mb-6">
                <i data-lucide="key" class="w-7 h-7"></i>
            </div>
            <h2 class="font-display text-3xl font-black uppercase tracking-tight text-walnut-950">
                Lupa Password
            </h2>
            <p class="text-sm text-muted font-medium">
                Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
            </p>
        </div>

        <form class="relative z-10 mt-8 space-y-6" action="{{ route('password.email') }}" method="POST">
            @csrf

            @if (session('status'))
                <div class="p-4 bg-cream-50 border-l-4 border-gold-500 text-walnut-900 text-sm font-semibold">
                    {{ session('status') }}
                </div>
            @endif

            <div class="space-y-4">
                <div class="space-y-2">
                    <label for="email" class="text-[0.65rem] uppercase tracking-widest text-walnut-800 font-bold block">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-muted">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="block w-full pl-11 pr-4 py-3.5 bg-cream-100 border border-walnut-800/20 text-sm font-semibold text-walnut-900 placeholder-walnut-800/40 focus:outline-none focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500 focus:bg-cream-50 transition-all duration-300" 
                            placeholder="nama@email.com" value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="text-red-600 text-xs font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-4 px-4 border border-transparent text-sm font-bold text-gold-500 bg-walnut-900 hover:bg-gold-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gold-500 transition-all duration-300 uppercase tracking-wider">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    Kirim Tautan
                </button>
            </div>
            
            <div class="text-center pt-2">
                <p class="text-sm text-muted font-medium">
                    Ingat password Anda? 
                    <a href="{{ route('login') }}" class="font-bold text-gold-600 hover:text-gold-500 transition">Login sekarang</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
