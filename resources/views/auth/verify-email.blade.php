@extends('layouts.app')

@section('title', 'Verifikasi Email - DjudasMS')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md bg-cream-50 border border-walnut-800/10 p-8 md:p-10 space-y-8">
        <!-- Header -->
        <div class="text-center space-y-4">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gold-500/10">
                <i data-lucide="mail-check" class="w-7 h-7 text-gold-600"></i>
            </div>
            <div class="space-y-2">
                <span class="text-[0.65rem] uppercase tracking-[0.4em] text-gold-600 font-bold bg-gold-500/10 px-3.5 py-1.5 inline-block">Verifikasi Diperlukan</span>
                <h2 class="font-display text-3xl font-black uppercase tracking-tight text-walnut-950">Verifikasi Email</h2>
                <p class="text-xs text-muted font-normal">Terima kasih telah mendaftar! Masukkan 6-digit kode OTP yang telah kami kirimkan ke email Anda untuk memverifikasi akun.</p>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
            <div class="bg-cream-50 border-l-4 border-gold-500 p-4 flex items-start gap-3">
                <span class="text-gold-600 mt-0.5 shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </span>
                <p class="text-xs text-walnut-900 font-semibold">Kode OTP baru telah dikirim ke alamat email Anda.</p>
            </div>
        @endif

        @if ($errors->has('otp_code'))
            <div class="bg-cream-50 border-l-4 border-red-600 p-4 flex items-start gap-3">
                <span class="text-red-600 mt-0.5 shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                </span>
                <p class="text-xs text-red-700 font-semibold">{{ $errors->first('otp_code') }}</p>
            </div>
        @endif

        <!-- OTP Form -->
        <form action="{{ route('verification.verify.otp') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="otp_code" class="block text-[0.65rem] font-bold text-walnut-800 uppercase tracking-widest mb-2 ml-1">Kode OTP (6 Digit)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="key" class="w-5 h-5 text-muted"></i>
                    </div>
                    <input type="text" name="otp_code" id="otp_code" maxlength="6" pattern="\d{6}" required
                        class="w-full pl-12 pr-4 py-3.5 bg-cream-100 border border-walnut-800/20 text-lg tracking-[0.5em] font-bold text-walnut-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-cream-50 focus:border-gold-500 transition text-center"
                        placeholder="••••••">
                </div>
            </div>

            <button type="submit" 
                class="w-full py-4 bg-walnut-900 text-gold-500 font-bold uppercase text-xs tracking-widest hover:bg-gold-600 hover:text-white transition duration-300 flex items-center justify-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4"></i> Verifikasi Kode
            </button>
        </form>

        <hr class="border-walnut-800/10 border-dashed">

        <!-- Info Box & Resend -->
        <div class="bg-cream-100 border border-walnut-800/10 p-5 text-xs space-y-3 text-muted">
            <div>
                <span class="font-bold text-walnut-900 block uppercase tracking-wider text-[0.6rem]">Belum menerima email?</span>
                <p class="text-[0.7rem] font-medium text-walnut-900/70 mt-1">Periksa folder spam. Jika tidak ada, klik tombol di bawah untuk meminta ulang kode.</p>
            </div>
            
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" 
                    class="w-full py-3 bg-cream-50 text-walnut-900 border border-walnut-800/20 font-bold uppercase text-[0.65rem] tracking-widest hover:border-gold-500 hover:text-gold-600 transition duration-300 flex items-center justify-center gap-1.5">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Minta Kode Baru
                </button>
            </form>
        </div>

        <!-- Logout -->
        <div class="text-center pt-2">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs text-muted font-semibold hover:text-walnut-900 transition inline-flex items-center gap-1.5">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Keluar dari Akun
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
