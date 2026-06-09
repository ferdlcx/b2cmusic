@extends('layouts.app')

@section('title', 'Daftar - DjudasMS')

@section('content')
<div class="flex items-center justify-center -mt-8 py-12">
    <div class="w-full max-w-[1200px] grid lg:grid-cols-2 bg-cream-50 overflow-hidden border border-walnut-800/10">
        <!-- Right Side: Editorial Image (Swapped for Register) -->
        <div class="hidden lg:block relative bg-walnut-900 overflow-hidden lg:order-2">
            <img src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?auto=format&fit=crop&w=1200&q=80" 
                 alt="Premium Vinyl Player" 
                 class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-60" />
            <div class="absolute inset-0 flex flex-col justify-between p-12">
                <span class="text-gold-500 font-bold uppercase tracking-[0.3em] text-[0.65rem] text-right">Join The Club</span>
                <div class="text-right">
                    <h2 class="font-display text-5xl font-black uppercase text-cream-50 leading-[0.9] tracking-tighter">
                        Sonic <br> <span class="text-gold-500">Purity.</span>
                    </h2>
                    <p class="mt-4 text-cream-50/70 text-sm max-w-sm ml-auto">
                        Bergabunglah dengan komunitas eksklusif pecinta audio analog dan instrumen premium.
                    </p>
                </div>
            </div>
        </div>

        <!-- Left Side: Minimal Form -->
        <div class="p-8 md:p-16 lg:p-20 flex flex-col justify-center lg:order-1">
            <div class="space-y-2 mb-10 text-center lg:text-left">
                <h2 class="font-display text-3xl font-black uppercase tracking-tighter text-walnut-950">Buat Akun</h2>
                <p class="text-xs text-muted font-medium tracking-wide">Daftar untuk mulai menikmati koleksi kami.</p>
            </div>

            <!-- Form -->
            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf
                
                <!-- Nama -->
                <div class="space-y-2">
                    <label for="name" class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-800 font-bold block">Nama Lengkap</label>
                    <div class="relative">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                            class="w-full bg-transparent border-b border-walnut-800/20 py-3 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-sm font-medium @error('name') border-red-500 @enderror" 
                            placeholder="John Doe" />
                    </div>
                    @error('name')
                        <span class="text-[0.6rem] text-red-600 font-bold block mt-1 uppercase tracking-widest">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="space-y-2 pt-2">
                    <label for="email" class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-800 font-bold block">Alamat Email</label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full bg-transparent border-b border-walnut-800/20 py-3 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-sm font-medium @error('email') border-red-500 @enderror" 
                            placeholder="nama@email.com" />
                    </div>
                    @error('email')
                        <span class="text-[0.6rem] text-red-600 font-bold block mt-1 uppercase tracking-widest">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-800 font-bold block">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full bg-transparent border-b border-walnut-800/20 py-3 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-sm font-medium @error('password') border-red-500 @enderror" 
                                placeholder="••••••••" />
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="text-[0.65rem] uppercase tracking-[0.2em] text-walnut-800 font-bold block">Konfirmasi</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full bg-transparent border-b border-walnut-800/20 py-3 text-walnut-950 focus:outline-none focus:border-gold-500 transition text-sm font-medium" 
                                placeholder="••••••••" />
                        </div>
                    </div>
                </div>
                @error('password')
                    <span class="text-[0.6rem] text-red-600 font-bold block mt-1 uppercase tracking-widest">{{ $message }}</span>
                @enderror

                <!-- Submit -->
                <button type="submit" 
                    class="w-full py-4 mt-6 bg-walnut-900 text-gold-500 font-bold uppercase text-[0.7rem] tracking-[0.2em] hover:bg-gold-600 hover:text-white transition duration-500 flex items-center justify-center gap-2">
                    Daftar Sekarang
                </button>
            </form>

            <!-- Footer -->
            <div class="text-center pt-8 mt-auto">
                <p class="text-[0.65rem] text-muted font-bold uppercase tracking-widest">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-walnut-950 hover:text-gold-600 transition">Masuk di Sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
