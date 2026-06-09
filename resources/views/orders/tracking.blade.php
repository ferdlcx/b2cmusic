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

        <!-- Leaflet Map Container -->
        <div id="tracking-map" class="w-full h-[500px] bg-walnut-100 z-0 border-b border-walnut-800/10"></div>

        <div class="p-10 max-w-2xl mx-auto">
            <div class="relative">
                <div class="absolute top-0 bottom-0 left-[15px] w-0.5 bg-walnut-800/10"></div>

                <!-- Timeline Items -->
                <div class="relative flex items-start gap-6 mb-10">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 border border-emerald-500 flex items-center justify-center shrink-0 z-10 shadow-sm">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-walnut-950 text-[0.8rem] uppercase tracking-widest">Pesanan Dibuat</h4>
                        <p class="text-[0.7rem] font-medium text-muted mt-1">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                </div>

                <div class="relative flex items-start gap-6 mb-10">
                    <div class="w-8 h-8 rounded-full {{ in_array($order->status, ['processing', 'shipped', 'completed']) ? 'bg-emerald-50 border border-emerald-500' : 'bg-cream-50 border border-walnut-800/20' }} flex items-center justify-center shrink-0 z-10 shadow-sm">
                        <i data-lucide="package" class="w-4 h-4 {{ in_array($order->status, ['processing', 'shipped', 'completed']) ? 'text-emerald-600' : 'text-walnut-400' }}"></i>
                    </div>
                    <div>
                        <h4 class="font-bold {{ in_array($order->status, ['processing', 'shipped', 'completed']) ? 'text-walnut-950' : 'text-walnut-400' }} text-[0.8rem] uppercase tracking-widest">Pesanan Diproses</h4>
                        @if($order->status !== 'pending')
                            <p class="text-[0.7rem] font-medium text-muted mt-1">Pesanan sedang dikemas oleh penjual.</p>
                        @endif
                    </div>
                </div>

                <div class="relative flex items-start gap-6 mb-10">
                    <div class="w-8 h-8 rounded-full {{ in_array($order->status, ['shipped', 'completed']) ? 'bg-gold-50 border border-gold-500' : 'bg-cream-50 border border-walnut-800/20' }} flex items-center justify-center shrink-0 z-10 shadow-sm">
                        <i data-lucide="truck" class="w-4 h-4 {{ in_array($order->status, ['shipped', 'completed']) ? 'text-gold-600' : 'text-walnut-400' }}"></i>
                    </div>
                    <div>
                        <h4 class="font-bold {{ in_array($order->status, ['shipped', 'completed']) ? 'text-walnut-950' : 'text-walnut-400' }} text-[0.8rem] uppercase tracking-widest">Sedang Dalam Perjalanan</h4>
                        @if(in_array($order->status, ['shipped', 'completed']) && $order->shipment && $order->shipment->shipped_at)
                            <p class="text-[0.7rem] font-medium text-muted mt-1">Diserahkan ke kurir pada {{ \Carbon\Carbon::parse($order->shipment->shipped_at)->translatedFormat('d M Y, H:i') }}</p>
                        @endif
                    </div>
                </div>

                <div class="relative flex items-start gap-6">
                    <div class="w-8 h-8 rounded-full {{ ($order->shipment && $order->shipment->status === 'delivered') ? 'bg-emerald-50 border border-emerald-500' : 'bg-cream-50 border border-walnut-800/20' }} flex items-center justify-center shrink-0 z-10 shadow-sm">
                        <i data-lucide="home" class="w-4 h-4 {{ ($order->shipment && $order->shipment->status === 'delivered') ? 'text-emerald-600' : 'text-walnut-400' }}"></i>
                    </div>
                    <div class="flex-1 flex flex-col gap-4">
                        <div>
                            <h4 class="font-bold {{ ($order->shipment && $order->shipment->status === 'delivered') ? 'text-walnut-950' : 'text-walnut-400' }} text-[0.8rem] uppercase tracking-widest">Pesanan Tiba di Tujuan</h4>
                            @if($order->shipment && $order->shipment->status === 'delivered' && $order->shipment->delivered_at)
                                <p class="text-[0.7rem] font-medium text-muted mt-1">Paket tiba pada {{ \Carbon\Carbon::parse($order->shipment->delivered_at)->translatedFormat('d M Y, H:i') }}</p>
                            @endif
                        </div>
                        
                        @if($order->status === 'shipped' && $order->shipment && $order->shipment->status === 'delivered')
                            <div class="mt-4 p-5 bg-walnut-50 border border-walnut-800/20">
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
    </div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var originLat = {{ $originLat }};
        var originLng = {{ $originLng }};
        var destLat = {{ $destLat }};
        var destLng = {{ $destLng }};

        // Initialize map
        var map = L.map('tracking-map');

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // Custom icons
        var storeIcon = L.divIcon({
            html: `<div class="w-8 h-8 bg-walnut-900 text-gold-500 rounded-full flex items-center justify-center shadow-lg border border-walnut-800"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></div>`,
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        var customerIcon = L.divIcon({
            html: `<div class="w-8 h-8 bg-walnut-900 text-gold-500 rounded-full flex items-center justify-center shadow-lg border border-walnut-800"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></div>`,
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        var truckIcon = L.divIcon({
            html: `<div class="w-10 h-10 bg-gold-500 text-walnut-950 rounded-full flex items-center justify-center shadow-lg border border-walnut-800 relative"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg></div>`,
            className: '',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        // Add markers
        var originMarker = L.marker([originLat, originLng], {icon: storeIcon}).addTo(map)
            .bindPopup('<b>Toko DjudasMS</b><br>Jakarta Barat');

        var destMarker = L.marker([destLat, destLng], {icon: customerIcon}).addTo(map)
            .bindPopup('<b>Alamat Pengiriman</b><br>{{ $order->address->city ?? "Tujuan" }}');

        // Draw line between origin and destination
        var latlngs = [
            [originLat, originLng],
            [destLat, destLng]
        ];

        var polyline = L.polyline(latlngs, {
            color: '#b6905b', 
            weight: 4,
            dashArray: '10, 10',
            opacity: 0.8
        }).addTo(map);

        // Fit bounds to show both markers
        map.fitBounds(polyline.getBounds(), {padding: [50, 50]});

        // Add Truck marker depending on status
        var status = '{{ $order->status }}';
        var shipmentStatus = '{{ $order->shipment->status ?? "" }}';
        if (status === 'shipped' && shipmentStatus !== 'delivered') {
            // Place truck somewhere in the middle (50%)
            var midLat = originLat + ((destLat - originLat) * 0.5);
            var midLng = originLng + ((destLng - originLng) * 0.5);
            L.marker([midLat, midLng], {icon: truckIcon}).addTo(map)
                .bindPopup('<b>Sedang dalam pengiriman</b>');
        } else if (status === 'completed' || shipmentStatus === 'delivered') {
            L.marker([destLat, destLng], {icon: truckIcon}).addTo(map)
                .bindPopup('<b>Paket telah tiba</b>');
        }
    });
</script>
@endsection
