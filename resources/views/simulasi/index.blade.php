@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8 min-h-[80vh]" x-data="simulator()">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-tight text-walnut-950 flex items-center gap-2">
                <i data-lucide="truck" class="w-6 h-6 text-gold-600"></i> Biteship Webhook Simulator
            </h1>
            <p class="text-[0.7rem] font-bold text-muted mt-1">Menguji webhook order.status dan order.price (Mendukung Payload Asli Biteship)</p>
        </div>
        <form action="{{ route('simulasi.clear') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus seluruh riwayat pesanan dari database lokal? Data yang dihapus tidak bisa dikembalikan.');">
            @csrf
            <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 rounded-xl text-[0.65rem] font-bold uppercase tracking-widest transition flex items-center gap-2">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Clear All Orders
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-800/10 text-emerald-800 rounded-xl text-sm font-bold shadow-sm flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-800/10 text-red-800 rounded-xl text-sm font-bold shadow-sm flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-walnut-800/10 shadow-sm overflow-hidden rounded-xl min-h-[500px] flex flex-col">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left text-sm whitespace-nowrap min-w-[1000px]">
                <thead class="bg-walnut-900/5 border-b border-walnut-800/10 text-walnut-800 text-[0.65rem] font-bold uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4">ID & Resi</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Penerima</th>
                        <th class="px-6 py-4">Kurir & Ongkir</th>
                        <th class="px-6 py-4">Status & Sumber</th>
                        <th class="px-6 py-4 text-center">Aksi Simulasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-walnut-800/5 text-walnut-950 font-medium text-[0.75rem]">
                    @forelse($orders as $order)
                        @php
                            $address = $order->address;
                            $bobot = $order->items->sum(function($i) { return ($i->product->weight ?? 1000) * $i->quantity; }) / 1000;
                            $qty = $order->items->sum('quantity');
                            $biteshipId = $order->biteship_order_id ?? ('SIM-' . $order->id);
                            
                            $currentStatus = $order->shipment->status ?? 'pending';
                            
                            $statusPill = match(strtolower($currentStatus)) {
                                'delivered' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                'returnintransit', 'return_in_transit' => 'bg-purple-50 text-purple-600 border-purple-200',
                                'returned' => 'bg-rose-50 text-rose-600 border-rose-200',
                                'dropping_off', 'droppingoff' => 'bg-blue-50 text-blue-600 border-blue-200',
                                'picked' => 'bg-teal-50 text-teal-600 border-teal-200',
                                'picking_up', 'pickingup' => 'bg-amber-50 text-amber-600 border-amber-200',
                                'allocated' => 'bg-orange-50 text-orange-600 border-orange-200',
                                'confirmed' => 'bg-gray-100 text-gray-700 border-gray-200',
                                default => 'bg-gray-50 text-gray-600 border-gray-200'
                            };

                            $statusLabel = match(strtolower($currentStatus)) {
                                'delivered' => 'Berhasil Dikirim',
                                'return_in_transit', 'returnintransit' => 'Dalam Retur',
                                'returned' => 'Dikembalikan',
                                'dropping_off', 'droppingoff' => 'Dalam Pengantaran',
                                'picked' => 'Barang Dijemput',
                                'picking_up', 'pickingup' => 'Menuju Penjemputan',
                                'allocated' => 'Alokasi Kurir',
                                'confirmed' => 'Terkonfirmasi',
                                'processing' => 'Diproses',
                                'shipped' => 'Dikirim',
                                'pending' => 'Pending',
                                default => ucfirst($currentStatus)
                            };

                            // Strict step-by-step transitions matching Biteship Test Dashboard
                            $cs = strtolower($currentStatus);
                            if ($cs === 'picking_up') $cs = 'pickingup';
                            if ($cs === 'dropping_off') $cs = 'droppingoff';
                            if ($cs === 'return_in_transit') $cs = 'returnintransit';

                            $nextActions = [];
                            if (in_array($cs, ['pending', 'paid', 'processing'])) {
                                $nextActions = ['confirmed' => 'Confirm'];
                            } elseif ($cs === 'confirmed') {
                                $nextActions = ['allocated' => 'Allocate'];
                            } elseif ($cs === 'allocated') {
                                $nextActions = ['pickingUp' => 'Pick Up'];
                            } elseif ($cs === 'pickingup') {
                                $nextActions = ['picked' => 'Picked'];
                            } elseif ($cs === 'picked') {
                                $nextActions = ['droppingOff' => 'Drop Off'];
                            } elseif ($cs === 'droppingoff' || $cs === 'shipped') {
                                $nextActions = [
                                    'delivered' => 'Deliver',
                                    'returnInTransit' => 'Return'
                                ];
                            } elseif ($cs === 'returnintransit') {
                                $nextActions = ['returned' => 'Mark Returned'];
                            } elseif ($cs === 'delivered' && $order->status !== 'completed') {
                                // Provide an internal action to simulate user clicking "Pesanan Diterima"
                                $nextActions = ['completed' => 'Complete Order'];
                            }
                        @endphp
                        <tr class="hover:bg-walnut-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="font-bold text-walnut-950">{{ $biteshipId }}</span><br>
                                <span class="text-[0.65rem] text-walnut-500 font-mono">{{ $order->order_code }}</span>
                                <div class="mt-1.5 flex items-center gap-1.5">
                                    <i data-lucide="barcode" class="w-3.5 h-3.5 text-gold-500"></i>
                                    <span class="text-[0.65rem] font-bold text-walnut-800">{{ $order->waybill_id ?? $order->shipment->tracking_number ?? 'Menunggu Resi' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-1.5 text-[0.65rem]">
                                        <i data-lucide="clock" class="w-3 h-3 text-walnut-400"></i>
                                        <span class="text-walnut-600">Dibuat: <strong class="text-walnut-900">{{ $order->created_at->format('d M Y, H:i') }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[0.65rem]">
                                        <i data-lucide="refresh-cw" class="w-3 h-3 text-walnut-400"></i>
                                        <span class="text-walnut-600">Update: <strong class="text-walnut-900">{{ ($order->shipment->updated_at ?? $order->updated_at)->format('d M Y, H:i') }}</strong></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-walnut-950">{{ $address->recipient_name ?? $order->user->name }}</span><br>
                                <span class="text-[0.65rem] text-walnut-500">{{ $address->phone ?? $order->user->phone }}</span>
                                <div class="mt-1 text-[0.65rem] text-walnut-600">
                                    {{ $address->district ?? $address->city ?? 'Jakarta' }} ({{ $address->postal_code ?? '12345' }})
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 border border-walnut-800/10 rounded-full text-[0.6rem] uppercase font-bold text-walnut-700 bg-white shadow-sm mb-1">
                                    {{ $order->shipment->courier ?? 'JNE' }} - {{ $order->shipment->service ?? 'REG' }}
                                </span>
                                <div class="text-[0.65rem] text-walnut-600">
                                    Total: <strong class="text-walnut-900">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</strong>
                                </div>
                                <div class="text-[0.65rem] text-walnut-500">
                                    {{ $qty }} item ({{ number_format($bobot, 2) }} kg)
                                </div>
                            </td>
                            <td class="px-6 py-4 space-y-2">
                                <div>
                                    <span class="inline-flex px-2 py-1 rounded text-[0.65rem] font-bold border uppercase tracking-wide {{ $statusPill }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <div>
                                    @if($order->is_simulation)
                                        <span class="inline-flex px-2 py-0.5 rounded text-[0.6rem] font-black uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                                            Simulasi Lokal
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded text-[0.6rem] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                            API Biteship
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    @if(count($nextActions) > 0)
                                        <div class="flex flex-col gap-1.5 w-full max-w-[140px]">
                                            @php
                                                $firstTarget = array_key_first($nextActions);
                                            @endphp
                                            <form action="{{ route('simulasi.webhook.status') }}" method="POST" class="w-full">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $biteshipId }}">
                                                <input type="hidden" name="status" value="{{ $firstTarget }}">
                                                <button type="submit" class="w-full py-1.5 px-3 bg-walnut-900 hover:bg-gold-600 text-white rounded-lg text-[0.65rem] font-bold uppercase tracking-widest transition flex items-center justify-center gap-1.5 shadow-sm group">
                                                    <i data-lucide="play" class="w-3.5 h-3.5 text-gold-500 group-hover:text-white transition"></i> Update Status
                                                </button>
                                            </form>
                                            
                                            @if(isset($nextActions['returnInTransit']))
                                            <form action="{{ route('simulasi.webhook.status') }}" method="POST" class="w-full">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $biteshipId }}">
                                                <input type="hidden" name="status" value="returnInTransit">
                                                <button type="submit" class="w-full py-1.5 px-3 border border-rose-200 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg text-[0.65rem] font-bold uppercase tracking-widest transition flex items-center justify-center gap-1.5">
                                                    <i data-lucide="corner-down-right" class="w-3.5 h-3.5"></i> Return Status
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    @else
                                        <div class="px-3 py-1.5 bg-walnut-50 border border-walnut-800/10 text-walnut-400 rounded-lg text-[0.65rem] font-bold uppercase tracking-widest flex items-center gap-1.5">
                                            <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Selesai
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-center gap-1.5 w-full max-w-[140px] pt-1">
                                        <button @click="openPriceModal('{{ $biteshipId }}', {{ $order->shipping_cost }})" class="flex-1 py-1.5 border border-gold-500 text-gold-600 hover:bg-gold-50 rounded-lg text-[0.6rem] font-bold uppercase tracking-wider transition flex items-center justify-center" title="Update Harga Ongkir">
                                            <i data-lucide="dollar-sign" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button type="button" onclick="alert('Tags successfully simulated!')" class="flex-1 py-1.5 border border-walnut-800/20 text-walnut-600 hover:bg-walnut-50 rounded-lg text-[0.6rem] font-bold uppercase tracking-wider transition flex items-center justify-center" title="Tambah Tags Webhook">
                                            <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-walnut-400">
                                    <i data-lucide="package-x" class="w-10 h-10 mb-3 opacity-50"></i>
                                    <p class="text-sm font-bold uppercase tracking-widest">Tidak ada pesanan</p>
                                    <p class="text-xs mt-1 text-muted">Belum ada pesanan untuk disimulasikan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-walnut-800/10">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Penjelasan Fungsi Simulator -->
    <div class="mt-8 bg-walnut-900 border border-walnut-800 rounded-2xl p-5 sm:p-6 shadow-xl relative overflow-hidden">
        <i data-lucide="shield-question" class="absolute -right-4 -bottom-4 w-32 h-32 text-walnut-800/30 opacity-50 transform -rotate-12 pointer-events-none"></i>
        <div class="relative z-10 flex gap-4">
            <div class="w-12 h-12 bg-gold-500/10 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="info" class="w-6 h-6 text-gold-500"></i>
            </div>
            <div class="space-y-3">
                <h2 class="text-sm font-black uppercase tracking-widest text-gold-500">Kenapa Simulator Ini Dibuat?</h2>
                <div class="text-[0.75rem] leading-relaxed text-cream-50/80 space-y-2">
                    <p>Sistem <strong>DjudasMS sudah terintegrasi secara penuh (Real-Time) dengan Biteship</strong>. Dalam alur normal, kurir Biteship akan memperbarui status resi dan mengirimkan <em>webhook</em> ke sistem kita.</p>
                    <p><strong>Lalu kenapa tidak menggunakan Biteship saja untuk pengujian (testing)?</strong></p>
                    <ul class="list-disc list-inside space-y-1 pl-1">
                        <li>Mengubah status pengiriman secara manual di mode Sandbox/Testing <strong>membutuhkan akses login ke Dashboard Developer Biteship</strong>.</li>
                        <li>Sebagai langkah keamanan, kredensial dan akses dashboard Biteship tidak dapat diberikan sembarangan kepada pihak penguji eksternal.</li>
                    </ul>
                    <p class="pt-2"><strong>Solusi:</strong> Halaman simulator ini dirancang khusus untuk mempermudah proses pengujian. Saat tombol ditekan, sistem DjudasMS akan merakit (build) payload JSON yang format dan strukturnya <strong class="text-gold-400">100% sama persis</strong> dengan data yang dikirimkan oleh server Biteship, lalu menembakkannya (POST) ke <em>webhook endpoint</em> lokal. Ini menjamin pengujian fitur asinkron berjalan valid tanpa membahayakan kunci akses utama.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Update Price -->
    <div x-show="priceModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-walnut-950/50 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-[400px] border border-walnut-800/10" @click.away="priceModalOpen = false">
            <h3 class="font-display font-black text-lg text-walnut-950 uppercase tracking-tight mb-2">Update Order Price</h3>
            <p class="text-[0.7rem] text-muted mb-4">Simulasikan webhook order.price jika biaya pengiriman aktual berbeda (misal karena perbedaan berat).</p>
            
            <form action="{{ route('simulasi.webhook.price') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" x-model="selectedOrderId">
                
                <div class="mb-4">
                    <label class="block text-[0.65rem] font-bold uppercase tracking-widest text-walnut-600 mb-1">New Price (Rp)</label>
                    <input type="number" name="price" x-model="selectedPrice" class="w-full px-4 py-2 bg-cream-50 border border-walnut-800/20 rounded-xl focus:border-gold-500 focus:outline-none transition font-bold" required>
                </div>
                
                <div class="flex gap-2 justify-end">
                    <button type="button" @click="priceModalOpen = false" class="px-4 py-2 bg-cream-100 text-walnut-600 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-cream-200 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-gold-500 text-white rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-gold-600 transition shadow-lg shadow-gold-500/30">Update Price</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function simulator() {
        return {
            priceModalOpen: false,
            selectedOrderId: '',
            selectedPrice: 0,
            
            openPriceModal(id, currentPrice) {
                this.selectedOrderId = id;
                this.selectedPrice = currentPrice;
                this.priceModalOpen = true;
            }
        }
    }
</script>
@endsection
