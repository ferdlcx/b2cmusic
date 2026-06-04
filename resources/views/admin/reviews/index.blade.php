@extends('admin.layouts.admin')

@section('title', 'Moderasi Ulasan - Admin DjudasMS')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6">
        <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Interaksi</span>
        <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Moderasi Ulasan</h1>
        <p class="text-xs text-slate-500">Moderasi ulasan produk yang masuk dari pelanggan sebelum ditampilkan ke publik.</p>
    </div>

    <!-- Table -->
    @if($reviews->isEmpty())
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-12 text-center text-slate-500">
            <i data-lucide="message-square" class="w-10 h-10 text-slate-350 mx-auto mb-3"></i>
            <p class="text-xs font-semibold">Belum ada ulasan masuk.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Pengulas & Produk</th>
                        <th class="px-6 py-4">Rating & Komentar</th>
                        <th class="px-6 py-4">Foto</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reviews as $review)
                        <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                            <td class="px-6 py-5">
                                <span class="font-bold text-slate-900 block">{{ $review->user->name }}</span>
                                <span class="text-[0.65rem] text-slate-400 font-semibold block">{{ $review->user->email }}</span>
                                <span class="text-[0.65rem] text-indigo-600 font-bold block mt-2 uppercase tracking-wide">Produk: {{ $review->product->name }}</span>
                            </td>
                            <td class="px-6 py-5 max-w-sm">
                                <!-- Stars -->
                                <div class="flex items-center gap-0.5 text-amber-400 mb-1.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                        @else
                                            <i data-lucide="star" class="w-3.5 h-3.5 text-slate-200"></i>
                                        @endif
                                    @endfor
                                </div>
                                <p class="text-xs text-slate-700 font-medium leading-relaxed">{{ $review->comment }}</p>
                                <span class="text-[0.6rem] text-slate-400 block mt-1">{{ $review->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="px-6 py-5">
                                @if($review->photo)
                                    <div class="w-12 h-12 rounded-lg overflow-hidden border border-slate-100 relative group shrink-0">
                                        <img src="{{ Storage::url($review->photo) }}" alt="Foto ulasan" class="w-full h-full object-cover" />
                                        <a href="{{ Storage::url($review->photo) }}" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white transition text-[0.55rem] font-bold">Zoom</a>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 font-semibold">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($review->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.6rem] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase">Pending</span>
                                @elseif($review->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.6rem] font-bold bg-emerald-50 text-emerald-700 border border-emerald-250 uppercase">Disetujui</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.6rem] font-bold bg-rose-50 text-rose-700 border border-rose-200 uppercase">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center space-y-2">
                                @if($review->status === 'pending')
                                    <form action="{{ route('admin.reviews.approve', $review->id) }}" method="POST" class="block">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-emerald-600 hover:underline">Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.reviews.reject', $review->id) }}" method="POST" class="block">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-rose-600 hover:underline">Tolak</button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 font-semibold">Moderasi Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pt-4">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection
