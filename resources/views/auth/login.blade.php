@extends('layouts.app')

@section('title', 'Masuk - DjudasMS')

@section('content')
<div class="flex items-center justify-center -mt-8 py-12">
    <div class="w-full max-w-[1200px] grid lg:grid-cols-2 bg-cream-50 overflow-hidden border border-walnut-800/10">
        <!-- Left Side: Editorial Image -->
        <div class="hidden lg:block relative bg-walnut-900 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?auto=format&fit=crop&w=1200&q=80" 
                 alt="Premium Piano" 
                 class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-50" />
            <div class="absolute inset-0 flex flex-col justify-between p-12">
                <span class="text-gold-500 font-bold uppercase tracking-[0.3em] text-[0.65rem]">DjudasMS Luxe</span>
                <div>
                    <h2 class="font-display text-5xl font-black uppercase text-cream-50 leading-[0.9] tracking-tighter">
                        Melody <br> in <span class="text-gold-500">Motion.</span>
                    </h2>
                    <p class="mt-4 text-cream-50/70 text-sm max-w-sm">
                        Akses koleksi eksklusif dan selesaikan transaksi dengan profil khusus anggota.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side: Minimal Form -->
        <div class="p-8 md:p-16 lg:p-20 flex flex-col justify-center">
            <div class="space-y-2 mb-10 text-center lg:text-left">
                <h2 class="font-display text-3xl font-black uppercase tracking-tighter text-walnut-950">Masuk Akun</h2>
                <p class="text-xs text-muted font-medium tracking-wide">Masukkan kredensial Anda untuk melanjutkan.</p>
            </div>

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-800 font-bold block">Alamat Email</label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-transparent border-b border-walnut-800/20 py-3 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-sm font-medium @error('email') border-red-500 @enderror" 
                            placeholder="nama@email.com" />
                    </div>
                    @error('email')
                        <span class="text-[0.65rem] text-red-600 font-bold block mt-1 uppercase tracking-widest">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-2 pt-2">
                    <div class="flex justify-between items-center">
                        <label for="password" class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-800 font-bold block">Password</label>
                        <a href="{{ route('password.request') }}" class="text-[0.6rem] font-bold text-gold-600 hover:text-walnut-950 uppercase tracking-widest transition">Lupa Password?</a>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                            class="w-full bg-transparent border-b border-walnut-800/20 py-3 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-sm font-medium" 
                            placeholder="••••••••" />
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center pt-2">
                    <input type="checkbox" name="remember" id="remember" class="w-3.5 h-3.5 text-gold-600 bg-cream-50 border-walnut-800/30 rounded-none focus:ring-gold-500 cursor-pointer" />
                    <label for="remember" class="ml-2.5 text-[0.7rem] uppercase tracking-widest text-muted font-bold cursor-pointer">Ingat saya</label>
                </div>

                <!-- Submit -->
                <button type="submit" 
                    class="w-full py-4 mt-4 bg-walnut-900 text-gold-500 font-bold uppercase text-[0.7rem] tracking-[0.2em] hover:bg-gold-600 hover:text-white transition duration-500 flex items-center justify-center gap-2">
                    Masuk
                </button>
            </form>

            <!-- Seeder info helper for easy student demonstration -->
            <div class="mt-10 border border-walnut-800/10 p-5 text-sm space-y-3 bg-cream-100">
                <span class="font-bold text-walnut-900 block uppercase tracking-widest text-[0.6rem]">Akun Demo Pengujian:</span>
                <div class="space-y-2 text-[0.7rem] font-medium text-muted uppercase tracking-wider">
                    <div class="flex justify-between border-b border-walnut-800/5 pb-2">
                        <span><strong class="text-gold-600">user@musicstore.com</strong></span>
                        <span>Pass: <strong class="text-walnut-900">password</strong></span>
                    </div>
                    <div class="flex justify-between">
                        <span><strong class="text-gold-600">admin@musicstore.com</strong></span>
                        <span>Pass: <strong class="text-walnut-900">password</strong></span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center pt-8 mt-auto">
                <p class="text-[0.65rem] text-muted font-bold uppercase tracking-widest">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-walnut-950 hover:text-gold-600 transition">Daftar Sekarang</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
