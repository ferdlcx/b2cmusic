@extends('layouts.app')

@section('title', 'Verifikasi Email - MusicStore Luxe')

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
                <p class="text-xs text-slate-500 font-normal">Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik link yang kami kirimkan.</p>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-start gap-3">
                <span class="text-emerald-600 mt-0.5 shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </span>
                <p class="text-xs text-emerald-700 font-semibold">Link verifikasi baru telah dikirim ke alamat email Anda.</p>
            </div>
        @endif

        <!-- Info Box -->
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-xs space-y-2 text-slate-500">
            <span class="font-bold text-slate-700 block uppercase tracking-wider text-[0.6rem]">Belum menerima email?</span>
            <p class="text-[0.7rem] font-semibold text-slate-600 leading-relaxed">Periksa folder spam atau junk Anda. Jika masih belum ditemukan, klik tombol di bawah untuk mengirim ulang email verifikasi.</p>
        </div>

        <!-- Resend Verification -->
        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <button type="submit" 
                class="w-full py-4.5 bg-indigo-600 text-white rounded-2xl font-semibold uppercase text-xs tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 transition duration-300 flex items-center justify-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i> Kirim Ulang Email Verifikasi
            </button>
        </form>

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
