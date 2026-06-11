@extends('layouts.app')

@section('title', 'Notifikasi Saya - DjudasMS')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-[0.65rem] uppercase tracking-[0.4em] text-gold-600 font-bold bg-gold-50 px-3.5 py-1.5 rounded-full inline-block">Kotak Masuk</span>
            <h1 class="font-display text-4xl font-black uppercase tracking-tight text-walnut-950 mt-2">Notifikasi Saya</h1>
            <p class="text-sm text-slate-500 font-normal">Pemberitahuan penting mengenai akun dan pesanan Anda.</p>
        </div>
        
        @if(auth()->user()->unreadNotifications->isNotEmpty())
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3 border border-gold-200 bg-gold-50/50 rounded-2xl text-xs font-bold uppercase tracking-wider text-gold-700 hover:bg-gold-600 hover:text-white transition">
                    <i data-lucide="check-check" class="w-4 h-4 mr-2"></i> Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <!-- Notifications List -->
    @if($notifications->isEmpty())
        <div class="text-center py-20 bg-white border border-slate-200/80 rounded-[32px] shadow-sm space-y-4">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-walnut-400 mx-auto">
                <i data-lucide="bell-off" class="w-7 h-7"></i>
            </div>
            <div class="space-y-1">
                <h3 class="font-display text-lg font-bold uppercase tracking-tight text-walnut-950">Kotak Masuk Bersih</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Anda tidak memiliki notifikasi baru saat ini.</p>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach($notifications as $notification)
                <div class="bg-white border border-slate-200/80 rounded-[24px] p-5 shadow-sm flex items-start gap-4 transition hover:bg-slate-50/30 {{ $notification->unread() ? 'border-l-4 border-l-indigo-600' : '' }}">
                    <!-- Icon -->
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $notification->unread() ? 'bg-gold-50 text-gold-600' : 'bg-slate-50 text-walnut-400' }}">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                    </div>

                    <!-- Message Content -->
                    <div class="flex-1 space-y-1.5 min-w-0">
                        <div class="flex items-center justify-between gap-3">
                            <h4 class="text-xs font-bold text-slate-850 uppercase tracking-wide truncate">
                                {{ $notification->data['title'] ?? 'Notifikasi Toko' }}
                            </h4>
                            <span class="text-[0.65rem] text-walnut-400 font-semibold shrink-0">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed">{{ $notification->data['body'] ?? ($notification->data['message'] ?? '') }}</p>

                        @if($notification->unread())
                            <div class="pt-2">
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[0.7rem] font-bold text-gold-600 hover:text-gold-700 flex items-center gap-1">
                                        <i data-lucide="check" class="w-3 h-3"></i> Tandai sudah dibaca
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
