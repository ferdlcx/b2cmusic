@extends('layouts.app')

@section('title', 'Reset Password - DjudasMS')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-[32px] border border-slate-200/80 shadow-xl shadow-slate-200/40 relative overflow-hidden">
        
        <!-- Decorative blobs -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-sky-500/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 text-center space-y-2">
            <div class="mx-auto w-14 h-14 bg-indigo-600 text-white flex items-center justify-center rounded-2xl shadow-lg shadow-indigo-600/30 mb-6">
                <i data-lucide="shield-check" class="w-7 h-7"></i>
            </div>
            <h2 class="font-display text-3xl font-black uppercase tracking-tight text-slate-900">
                Reset Password
            </h2>
            <p class="text-sm text-slate-500 font-medium">
                Buat kata sandi baru untuk akun Anda.
            </p>
        </div>

        <form class="relative z-10 mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
            @csrf
            
            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="space-y-4">
                <div class="space-y-2">
                    <label for="email" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 focus:bg-white transition-all duration-300" 
                            value="{{ old('email', request()->email) }}" readonly>
                    </div>
                    @error('email')
                        <p class="text-rose-500 text-xs font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Password Baru</label>
                    <div class="relative" x-data="{ show: false }">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input id="password" name="password" :type="show ? 'text' : 'password'" required 
                            class="block w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 focus:bg-white transition-all duration-300" 
                            placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-indigo-600 focus:outline-none transition">
                            <i x-show="!show" data-lucide="eye" class="w-4 h-4"></i>
                            <i x-show="show" data-lucide="eye-off" class="w-4 h-4" style="display: none;"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-rose-500 text-xs font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-[0.65rem] uppercase tracking-widest text-slate-400 font-bold block">Konfirmasi Password Baru</label>
                    <div class="relative" x-data="{ show: false }">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required 
                            class="block w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 focus:bg-white transition-all duration-300" 
                            placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-indigo-600 focus:outline-none transition">
                            <i x-show="!show" data-lucide="eye" class="w-4 h-4"></i>
                            <i x-show="show" data-lucide="eye-off" class="w-4 h-4" style="display: none;"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-4 px-4 border border-transparent text-sm font-bold rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-600/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 transition-all duration-300 uppercase tracking-wider">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Password Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
