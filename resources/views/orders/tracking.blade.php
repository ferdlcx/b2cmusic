@extends('layouts.app')

@section('title', 'Lacak Pesanan #' . $order->order_code)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('orders.show', $order->order_code) }}" class="inline-flex items-center text-[0.65rem] uppercase tracking-widest font-bold text-walnut-800 hover:text-gold-600 transition">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Kembali ke Detail Pesanan
        </a>
    </div>

    <div class="bg-cream-50 border border-walnut-800/10 shadow-sm overflow-hidden" x-data="trackingApp()" x-init="initApp()">
        <div class="p-8 border-b border-walnut-800/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl md:text-3xl font-black uppercase tracking-tighter text-walnut-950">Lacak Pengiriman</h1>
                <p class="text-[0.7rem] text-muted font-medium mt-1 uppercase tracking-widest">Resi: <span class="font-bold text-walnut-900">{{ $order->shipment->tracking_number ?? 'Belum ada resi' }}</span> ({{ strtoupper($order->shipment->courier ?? 'Kurir') }})</p>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="px-4 py-2 text-[0.65rem] uppercase tracking-widest font-bold" 
                    :class="status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : (status === 'shipped' ? 'bg-gold-50 text-gold-700 border border-gold-200' : 'bg-walnut-100 text-walnut-700 border border-walnut-200')"
                    x-text="status === 'completed' ? 'Selesai' : (status === 'shipped' ? 'Sedang Dikirim' : 'Diproses')">
                </span>
            </div>
        </div>

        <!-- Tracking Map -->
        <div class="border-b border-walnut-800/10 overflow-hidden" wire:ignore>
            <div id="tracking-map" style="height: 400px; width: 100%;"></div>
        </div>

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
        function trackingApp() {
            return {
                orderId: {{ $order->id }},
                status: '{{ $order->status }}',
                shipment_status: '{{ $order->shipment->status ?? "pending" }}',
                checkpoints: @json($checkpoints),
                map: null,
                polyline: null,
                markers: [],
                
                initApp() {
                    this.initMap();
                    // Polling API every 5 seconds
                    setInterval(() => {
                        this.fetchUpdate();
                    }, 5000);
                },
                
                initMap() {
                    let validCp = null;
                    if (this.checkpoints && this.checkpoints.length > 0) {
                        // Find the most recent valid checkpoint
                        for (let i = this.checkpoints.length - 1; i >= 0; i--) {
                            const cp = this.checkpoints[i];
                            if (cp.lat !== null && cp.lng !== null && !isNaN(cp.lat) && !isNaN(cp.lng)) {
                                validCp = cp;
                                break;
                            }
                        }
                    }
                    
                    // Fallback to center of Indonesia if absolutely no coordinates found
                    const startLat = validCp ? validCp.lat : -2.5;
                    const startLng = validCp ? validCp.lng : 118.0;
                    const startZoom = validCp ? 6 : 5;
                    
                    this.map = L.map('tracking-map').setView([startLat, startLng], startZoom);
                    // Premium CartoDB Map
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                        attribution: '&copy; <a href="https://carto.com/">CARTO</a>'
                    }).addTo(this.map);
                    
                    this.drawMapItems();
                },
                
                drawMapItems() {
                    this.markers.forEach(m => m.remove());
                    if (this.polyline) this.polyline.remove();
                    this.markers = [];
                    
                    const latLngs = [];
                    this.checkpoints.forEach((cp, idx) => {
                        if (cp.lat === null || cp.lng === null || isNaN(cp.lat) || isNaN(cp.lng)) return;
                        
                        const isLast = idx === this.checkpoints.length - 1;
                        const marker = L.circleMarker([cp.lat, cp.lng], {
                            radius: isLast ? 10 : 6,
                            fillColor: isLast ? '#d4a017' : '#3d2b1f',
                            color: '#fff',
                            weight: 2,
                            fillOpacity: 1
                        }).addTo(this.map);
                        
                        marker.bindPopup(`
                            <div style="font-family: sans-serif; min-width: 200px;">
                                <strong style="font-size: 13px;">${cp.status}</strong><br>
                                <span style="font-size: 11px; color: #666;">${cp.description}</span><br>
                                <span style="font-size: 11px; color: #888;">📍 ${cp.location}</span><br>
                                <span style="font-size: 10px; color: #999;">🕐 ${cp.datetime}</span>
                            </div>
                        `);
                        
                        this.markers.push(marker);
                        latLngs.push([cp.lat, cp.lng]);
                    });
                    
                    if (latLngs.length > 1) {
                        this.polyline = L.polyline(latLngs, {
                            color: '#d4a017',
                            weight: 3,
                            opacity: 0.7,
                            dashArray: '8, 8'
                        }).addTo(this.map);
                    }
                    
                    if (latLngs.length > 0) {
                        this.map.fitBounds(latLngs, { padding: [50, 50] });
                    }
                },
                
                fetchUpdate() {
                    fetch(`/order/${this.orderId}/track`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.status = data.status;
                        this.shipment_status = data.shipment_status;
                        if (JSON.stringify(this.checkpoints) !== JSON.stringify(data.checkpoints)) {
                            this.checkpoints = data.checkpoints;
                            this.drawMapItems();
                        }
                    })
                    .catch(err => console.error('Error fetching tracking updates:', err));
                }
            }
        }
        </script>

        <!-- Timeline from Checkpoints -->
        <div class="p-10 max-w-2xl mx-auto">
            <div class="relative">
                <div class="absolute top-0 bottom-0 left-[21px] w-[1px] bg-walnut-800/10"></div>

                <template x-for="(cp, index) in checkpoints" :key="index">
                    <div class="relative flex gap-8" :class="index !== checkpoints.length - 1 ? 'mb-12' : ''">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0 z-10 bg-cream-50"
                             :class="cp.completed ? (index === checkpoints.length - 1 ? 'border-emerald-500' : 'border-gold-500') : 'border-walnut-800/20'">
                             <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                  :class="cp.completed ? (index === checkpoints.length - 1 ? 'bg-emerald-50' : 'bg-gold-50') : 'bg-cream-100'">
                                <template x-if="cp.completed && index === checkpoints.length - 1">
                                    <svg class="w-4 h-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                </template>
                                <template x-if="cp.completed && index !== checkpoints.length - 1">
                                    <svg class="w-4 h-4 text-gold-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </template>
                                <template x-if="!cp.completed">
                                    <svg class="w-4 h-4 text-walnut-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle></svg>
                                </template>
                            </div>
                        </div>
                        <div class="flex-1 pt-1">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 mb-1.5">
                                <h4 class="font-bold text-walnut-950 text-sm tracking-wide flex items-center gap-3">
                                    <span x-text="cp.status"></span>
                                    <template x-if="cp.source === 'simulation'">
                                        <span class="bg-transparent text-walnut-500 font-mono text-[0.55rem] px-2 py-0.5 rounded-full tracking-widest border border-walnut-800/10 uppercase">[SIMULATION]</span>
                                    </template>
                                    <template x-if="cp.source === 'biteship'">
                                        <span class="bg-walnut-900 text-gold-500 font-mono text-[0.55rem] px-2 py-0.5 rounded-full tracking-widest uppercase border border-walnut-900">[PRODUCTION]</span>
                                    </template>
                                </h4>
                                <span class="text-[0.65rem] font-bold text-walnut-400 uppercase tracking-widest flex items-center gap-1.5 shrink-0">
                                    <i data-lucide="clock" class="w-3 h-3"></i> <span x-text="cp.datetime"></span>
                                </span>
                            </div>
                            <p class="text-[0.8rem] font-medium text-muted leading-relaxed" x-text="cp.description.replace(' [TEST MODE]', '')"></p>
                            <p class="text-[0.7rem] text-walnut-500 mt-2 flex items-center gap-1.5 font-medium"><i data-lucide="map-pin" class="w-3 h-3"></i> <span x-text="cp.location"></span></p>
                        </div>
                    </div>
                </template>

                <template x-if="status === 'shipped' && shipment_status === 'delivered'">
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
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

