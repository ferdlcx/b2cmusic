@extends('layouts.app')

@section('title', 'Verifikasi Email - DjudasMS')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-[36px] p-8 md:p-10 shadow-sm space-y-8">
        <!-- Header -->
        <div class="text-center space-y-4">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-50 rounded-full">
                <i data-lucide="mail-check" class="w-7 h-7 text-indigo-600"></i>
            </div>
            <div class="space-y-2">
                <span class="text-[0.65rem] uppercase tracking-[0.4em] text-indigo-600 font-bold bg-indigo-50 px-3.5 py-1.5 rounded-full inline-block">Verifikasi Diperlukan</span>
                <h2 class="font-display text-3xl font-black uppercase tracking-tight text-slate-950">Verifikasi Email</h2>
                <p class="text-xs text-slate-500 font-normal">Terima kasih telah mendaftar! Masukkan 6-digit kode OTP yang telah kami kirimkan ke email Anda untuk memverifikasi akun.</p>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-start gap-3">
                <span class="text-emerald-600 mt-0.5 shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </span>
                <p class="text-xs text-emerald-700 font-semibold">Kode OTP baru telah dikirim ke alamat email Anda.</p>
            </div>
        @endif

        @if ($errors->has('otp_code'))
            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 flex items-start gap-3">
                <span class="text-rose-600 mt-0.5 shrink-0">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                </span>
                <p class="text-xs text-rose-700 font-semibold">{{ $errors->first('otp_code') }}</p>
            </div>
        @endif

        <!-- OTP Form -->
        <form action="{{ route('verification.verify.otp') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="otp_code" class="block text-[0.65rem] font-bold text-slate-700 uppercase tracking-widest mb-2 ml-1">Kode OTP (6 Digit)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i data-lucide="key" class="w-5 h-5 text-slate-400"></i>
                    </div>
                    <input type="text" name="otp_code" id="otp_code" maxlength="6" pattern="\d{6}" required
                        class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-lg tracking-[0.5em] font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white focus:border-transparent transition text-center"
                        placeholder="••••••">
                </div>
            </div>

            <button type="submit" 
                class="w-full py-4.5 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4"></i> Verifikasi Kode
            </button>
        </form>

        <hr class="border-slate-100 border-dashed">

        <!-- Info Box & Resend -->
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-xs space-y-3 text-slate-500">
            <div>
                <span class="font-bold text-slate-700 block uppercase tracking-wider text-[0.6rem]">Belum menerima email?</span>
                <p class="text-[0.7rem] font-medium text-slate-600 mt-1">Periksa folder spam. Jika tidak ada, klik tombol di bawah untuk meminta ulang kode.</p>
            </div>
            
            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" 
                    class="w-full py-3 bg-white text-indigo-600 border border-indigo-200 rounded-xl font-bold uppercase text-[0.65rem] tracking-widest hover:bg-indigo-50 transition duration-300 flex items-center justify-center gap-1.5">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Minta Kode Baru
                </button>
            </form>
        </div>

        <!-- Logout -->
        <div class="text-center pt-2">
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs text-slate-500 font-semibold hover:text-slate-700 transition inline-flex items-center gap-1.5">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Keluar dari Akun
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
