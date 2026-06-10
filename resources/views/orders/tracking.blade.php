@extends('layouts.app')

@section('title', 'Lacak Pesanan #' . $order->order_code)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('orders.show', $order->order_code) }}" class="inline-flex items-center text-[0.65rem] uppercase tracking-widest font-bold text-walnut-800 hover:text-gold-600 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Kembali ke Detail Pesanan
        </a>
    </div>

    <div class="bg-cream-50 border border-walnut-800/10 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-walnut-800/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl md:text-3xl font-black uppercase tracking-tighter text-walnut-950">Lacak Pengiriman</h1>
                <p class="text-[0.7rem] text-muted font-medium mt-1 uppercase tracking-widest">Resi: <span class="font-bold text-walnut-900">{{ $order->shipment->tracking_number ?? 'Belum ada resi' }}</span> ({{ strtoupper($order->shipment->courier ?? 'Kurir') }})</p>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="px-4 py-2 text-[0.65rem] uppercase tracking-widest font-bold 
                    @if($order->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                    @elseif($order->status === 'shipped') bg-gold-50 text-gold-700 border border-gold-200
                    @else bg-walnut-100 text-walnut-700 border border-walnut-200 @endif">
                    @if($order->status === 'completed') Selesai
                    @elseif($order->status === 'shipped') Sedang Dikirim
                    @else Diproses @endif
                </span>
            </div>
        </div>

        <!-- Tracking Map -->
        <div class="border-b border-walnut-800/10 overflow-hidden">
            <div id="tracking-map" style="height: 400px; width: 100%;"></div>
        </div>

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkpoints = @json($checkpoints);
            if (checkpoints.length === 0) return;
            
            const map = L.map('tracking-map').setView([checkpoints[0].lat, checkpoints[0].lng], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);
            
            const latLngs = [];
            checkpoints.forEach((cp, idx) => {
                const isLast = idx === checkpoints.length - 1;
                const marker = L.circleMarker([cp.lat, cp.lng], {
                    radius: isLast ? 10 : 6,
                    fillColor: isLast ? '#d4a017' : '#3d2b1f',
                    color: '#fff',
                    weight: 2,
                    fillOpacity: 1
                }).addTo(map);
                
                marker.bindPopup(`
                    <div style="font-family: sans-serif; min-width: 200px;">
                        <strong style="font-size: 13px;">${cp.status}</strong><br>
                        <span style="font-size: 11px; color: #666;">${cp.description}</span><br>
                        <span style="font-size: 11px; color: #888;">📍 ${cp.location}</span><br>
                        <span style="font-size: 10px; color: #999;">🕐 ${cp.datetime}</span>
                    </div>
                `);
                
                latLngs.push([cp.lat, cp.lng]);
            });
            
            if (latLngs.length > 1) {
                L.polyline(latLngs, {
                    color: '#d4a017',
                    weight: 3,
                    opacity: 0.7,
                    dashArray: '8, 8'
                }).addTo(map);
            }
            
            map.fitBounds(latLngs, { padding: [50, 50] });
        });
        </script>

        <!-- Timeline from Checkpoints -->
        <div class="p-10 max-w-2xl mx-auto">
            <div class="relative">
                <div class="absolute top-0 bottom-0 left-[15px] w-0.5 bg-walnut-800/10"></div>

                @foreach($checkpoints as $index => $cp)
                    <div class="relative flex items-start gap-6 {{ !$loop->last ? 'mb-10' : '' }}">
                        <div class="w-8 h-8 rounded-full {{ $cp['completed'] ? ($loop->last ? 'bg-emerald-50 border border-emerald-500' : 'bg-gold-50 border border-gold-500') : 'bg-cream-50 border border-walnut-800/20' }} flex items-center justify-center shrink-0 z-10 shadow-sm">
                            @if($cp['completed'] && $loop->last)
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                            @elseif($cp['completed'])
                                <i data-lucide="check" class="w-4 h-4 text-gold-600"></i>
                            @else
                                <i data-lucide="circle" class="w-4 h-4 text-walnut-400"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-walnut-950 text-[0.8rem] uppercase tracking-widest">{{ $cp['status'] }}</h4>
                            <p class="text-[0.7rem] font-medium text-muted mt-1">{{ $cp['description'] }}</p>
                            <p class="text-[0.65rem] text-walnut-500 mt-0.5">📍 {{ $cp['location'] }}</p>
                            <p class="text-[0.65rem] text-walnut-400 mt-0.5">🕐 {{ $cp['datetime'] }}</p>
                        </div>
                    </div>
                @endforeach

                @if($order->status === 'shipped' && $order->shipment && $order->shipment->status === 'delivered')
                    <div class="mt-10 p-5 bg-walnut-50 border border-walnut-800/20">
                        <p class="text-[0.65rem] text-red-600 font-bold uppercase tracking-widest mb-2 flex items-center gap-2">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i> Perhatian PENTING
                        </p>
                        <p class="text-[0.7rem] text-muted font-medium mb-4 leading-relaxed">
                            Pastikan merekam video unboxing. Tanpa unboxing, komplain akan ditolak.
                        </p>
                        <form action="{{ route('orders.delivered', $order->id) }}" method="POST" onsubmit="return confirm('PENTING: Apakah Anda sudah merekam video unboxing dan yakin ingin menyelesaikan pesanan ini?');">
                            @csrf
                            <button type="submit" class="w-full py-3 bg-walnut-900 text-gold-500 rounded-none text-[0.65rem] uppercase tracking-widest font-bold hover:bg-gold-600 hover:text-white transition flex items-center justify-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> Konfirmasi Pesanan Diterima
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

