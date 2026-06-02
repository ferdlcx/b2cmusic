<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{ config('app.name', 'MusicStore') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
    </head>
    <body class="font-sans text-slate-900 overflow-x-hidden bg-white">
        <div class="relative z-10">
            <header class="max-w-[1440px] mx-auto px-6 lg:px-10 py-6 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <a href="/" class="inline-flex items-center gap-3 uppercase text-sm tracking-[0.55em] text-slate-900">
                    <span class="font-black text-lg">MUSICSTORE</span>
                    <span class="text-slate-500 text-[0.75rem] tracking-[0.7em]">LUXE</span>
                </a>
                <nav class="flex flex-wrap items-center gap-6 text-[0.82rem] uppercase tracking-[0.28em] text-slate-600">
                    <a href="#collections" class="hover:text-slate-900">COLLECTIONS</a>
                    <a href="#gallery" class="hover:text-slate-900">GALLERY</a>
                    <a href="#products" class="hover:text-slate-900">SHOP</a>
                    <a href="#story" class="hover:text-slate-900">STORY</a>
                </nav>
                <a href="/login" class="inline-flex items-center justify-center px-5 py-3 border border-slate-900 text-slate-900 uppercase text-xs tracking-[0.35em] hover:bg-slate-900 hover:text-white transition">MASUK</a>
            </header>

            <main class="max-w-[1440px] mx-auto px-6 lg:px-10 pb-16">
                <section class="min-h-screen grid gap-12 lg:grid-cols-[1.1fr_0.9fr] items-center py-16">
                    <div class="space-y-8">
                        <div class="inline-flex items-center gap-3 text-[0.75rem] uppercase tracking-[0.45em] text-slate-500">
                            <span>ARTISAN GUITARS</span>
                            <span class="h-px w-16 bg-slate-900"></span>
                        </div>
                        <div class="max-w-3xl">
                            <h1 class="text-5xl md:text-6xl lg:text-[5rem] leading-tight font-black uppercase tracking-[-0.05em] text-slate-950">Premium<br />Electric & Acoustic<br />Curated for Sound</h1>
                        </div>
                        <p class="max-w-2xl text-lg md:text-xl text-slate-600 leading-relaxed">A high-end music marketplace with editorial polish, clean Swiss typography, and rich instrument imagery. Explore luxury guitars, studio gear, and premium accessories.</p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#products" class="inline-flex items-center justify-center px-8 py-4 bg-slate-950 text-white uppercase text-sm tracking-[0.18em] hover:bg-slate-800 transition">Explore shop</a>
                            <a href="#gallery" class="inline-flex items-center justify-center px-8 py-4 border border-slate-300 text-slate-700 uppercase text-sm tracking-[0.18em] hover:border-slate-950 hover:text-slate-950 transition">View gallery</a>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                        <div class="overflow-hidden rounded-[40px] bg-slate-100 shadow-[0_30px_80px_rgba(15,23,42,0.08)]">
                            <img src="https://images.unsplash.com/photo-1511376777868-611b54f68947?auto=format&fit=crop&w=1200&q=80" alt="Luxury guitar" class="h-full w-full object-cover" />
                        </div>
                        <div class="grid gap-4 lg:grid-cols-2">
                            <div class="overflow-hidden rounded-[32px] bg-slate-100 shadow-[0_20px_50px_rgba(15,23,42,0.08)]">
                                <img src="https://images.unsplash.com/photo-1511974035430-5de47d3b95da?auto=format&fit=crop&w=1200&q=80" alt="Studio headphones" class="h-full w-full object-cover" />
                            </div>
                            <div class="rounded-[32px] bg-slate-950 p-8 flex flex-col justify-between text-white">
                                <div>
                                    <span class="text-sm uppercase tracking-[0.35em] text-slate-400">Signature drop</span>
                                    <h2 class="mt-5 text-3xl font-black uppercase tracking-[-0.04em]">Luxe Guitar Edit</h2>
                                </div>
                                <p class="mt-6 text-sm leading-relaxed text-slate-300">Timeless craftsmanship meets modern expression in a refined collection of premium electric and acoustic instruments.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="collections" class="py-16">
                    <div class="grid gap-10 lg:grid-cols-[1fr_0.85fr] items-end">
                        <div>
                            <h2 class="text-3xl md:text-4xl font-black uppercase tracking-[-0.04em] text-slate-950">Curated Collections</h2>
                            <p class="mt-4 text-slate-600 max-w-xl">From modern stage-ready guitars to elegant acoustic classics, each piece is selected for tone, presence, and detail.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach(['Electric','Acoustic','Studio','Boutique','Vintage','Accessories'] as $item)
                                <a href="#" class="group overflow-hidden rounded-[28px] border border-slate-200 bg-white p-8 shadow-[0_20px_45px_rgba(15,23,42,0.06)] transition hover:-translate-y-1 hover:border-slate-950">
                                    <div class="text-3xl font-black uppercase tracking-[0.1em] text-slate-950">{{ $item }}</div>
                                    <div class="mt-10 text-xs uppercase tracking-[0.4em] text-slate-500 opacity-0 group-hover:opacity-100 transition">→ Discover</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section id="gallery" class="py-16">
                    <div class="grid gap-6 lg:grid-cols-3">
                        <div class="overflow-hidden rounded-[40px] bg-slate-100 shadow-[0_30px_80px_rgba(15,23,42,0.08)] h-[540px]">
                            <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=1200&q=80" alt="Guitar close-up" class="h-full w-full object-cover" />
                        </div>
                        <div class="space-y-6">
                            <div class="overflow-hidden rounded-[40px] bg-slate-100 shadow-[0_30px_80px_rgba(15,23,42,0.08)] h-80">
                                <img src="https://images.unsplash.com/photo-1511376888252-2d45f494e4a2?auto=format&fit=crop&w=1200&q=80" alt="Vintage guitar" class="h-full w-full object-cover" />
                            </div>
                            <div class="overflow-hidden rounded-[40px] bg-slate-100 shadow-[0_30px_80px_rgba(15,23,42,0.08)] h-80">
                                <img src="https://images.unsplash.com/photo-1519669556875-76b9cec553b7?auto=format&fit=crop&w=1200&q=80" alt="Studio instrument" class="h-full w-full object-cover" />
                            </div>
                        </div>
                    </div>
                </section>

                <section id="products" class="py-16 bg-slate-50 rounded-[40px] border border-slate-200 p-10 shadow-[0_30px_80px_rgba(15,23,42,0.06)]">
                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-10">
                        <div>
                            <h2 class="text-4xl font-black uppercase tracking-[-0.04em] text-slate-950">Featured Instruments</h2>
                        </div>
                        <p class="max-w-2xl text-slate-600">A refined selection of premium gear ready for the stage, studio, or collectible display.</p>
                    </div>
                    <div class="grid gap-6 xl:grid-cols-3">
                        @foreach([
                            ['Fender Stratocaster', 'Rp 14.000.000', 'https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=1200&q=80'],
                            ['Martin D-28', 'Rp 28.500.000', 'https://images.unsplash.com/photo-1511376777868-611b54f68947?auto=format&fit=crop&w=1200&q=80'],
                            ['Neumann TLM 103', 'Rp 32.900.000', 'https://images.unsplash.com/photo-1512525541699-20fcb1c1ec5d?auto=format&fit=crop&w=1200&q=80'],
                        ] as $product)
                            <article class="group overflow-hidden rounded-[32px] bg-white border border-slate-200 shadow-[0_25px_70px_rgba(15,23,42,0.08)] transition hover:-translate-y-1">
                                <div class="h-72 overflow-hidden">
                                    <img src="{{ $product[2] }}" alt="{{ $product[0] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                                </div>
                                <div class="p-8">
                                    <span class="text-xs uppercase tracking-[0.35em] text-slate-500">Best Seller</span>
                                    <h3 class="mt-4 text-2xl font-black uppercase tracking-[-0.04em] text-slate-950">{{ $product[0] }}</h3>
                                    <p class="mt-3 text-lg font-semibold text-slate-700">{{ $product[1] }}</p>
                                    <div class="mt-8 flex items-center justify-between text-xs uppercase tracking-[0.3em] text-slate-500">
                                        <span>Shop now</span>
                                        <span class="inline-block h-px w-12 bg-slate-200"></span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section id="story" class="py-16">
                    <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                        <div class="space-y-6">
                            <p class="text-sm uppercase tracking-[0.4em] text-slate-500">OUR STORY</p>
                            <h2 class="text-4xl font-black uppercase tracking-[-0.04em] text-slate-950">Luxury gear with a modern editorial edge.</h2>
                            <p class="text-lg text-slate-600 leading-relaxed">A premium shopping destination for musicians who value craftsmanship, detail, and a clean visual experience. The layout is spacious, editorial, and easy to browse on mobile.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach([
                                ['01', 'Curated selection'],
                                ['02', 'Premium photography'],
                            ] as $item)
                                <div class="rounded-[32px] border border-slate-200 bg-white p-8 shadow-[0_20px_50px_rgba(15,23,42,0.06)]">
                                    <div class="text-4xl font-black text-slate-950">{{ $item[0] }}</div>
                                    <p class="mt-4 text-sm uppercase tracking-[0.35em] text-slate-500">{{ $item[1] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section class="py-16 rounded-[40px] bg-slate-950 text-white p-10 lg:p-16">
                    <div class="max-w-4xl mx-auto text-center">
                        <p class="text-sm uppercase tracking-[0.4em] text-slate-400">Newsletter</p>
                        <h2 class="mt-4 text-4xl font-black uppercase tracking-[-0.04em]">Be first to know about exclusive drops.</h2>
                        <form action="#" method="POST" class="mt-10 flex flex-col sm:flex-row gap-4">
                            <input type="email" placeholder="Your email address" class="min-w-0 flex-1 rounded-xl border border-slate-700 bg-slate-900 px-5 py-4 text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-400" />
                            <button type="submit" class="rounded-xl bg-white px-8 py-4 text-slate-950 font-semibold uppercase tracking-[0.18em] hover:bg-slate-200 transition">Subscribe</button>
                        </form>
                    </div>
                </section>

                <footer class="py-14 text-slate-500">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 border-t border-slate-200 pt-10">
                        <div class="grid grid-cols-2 gap-6 text-sm uppercase tracking-[0.2em]">
                            <div class="space-y-4">
                                <div class="font-semibold text-slate-900">Shop</div>
                                <div>Guitars</div>
                                <div>Keyboards</div>
                                <div>Drums</div>
                            </div>
                            <div class="space-y-4">
                                <div class="font-semibold text-slate-900">Brands</div>
                                <div>About</div>
                                <div>Contact</div>
                                <div>FAQ</div>
                            </div>
                        </div>
                        <div class="space-y-4 text-sm uppercase tracking-[0.2em]">
                            <div class="font-semibold text-slate-900">Follow</div>
                            <div>Instagram</div>
                            <div>TikTok</div>
                            <div>YouTube</div>
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </body>
</html>
