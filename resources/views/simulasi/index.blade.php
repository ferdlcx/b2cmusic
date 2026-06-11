@extends('layouts.app')

@section('content')
<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="simulator()">
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

    <div class="bg-white border border-walnut-800/10 shadow-sm overflow-hidden rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-white border-b border-walnut-800/10 text-walnut-600 text-[0.7rem] font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-4">Order ID<br><span class="text-walnut-400 font-normal">Reference ID</span></th>
                        <th class="px-4 py-4">Nomor Resi<br><span class="text-walnut-400 font-normal">Kurir - Layanan</span></th>
                        <th class="px-4 py-4">Tanggal Dibuat<br><span class="text-walnut-400 font-normal">Jam Dibuat</span></th>
                        <th class="px-4 py-4">Kec. Tujuan<br><span class="text-walnut-400 font-normal">Kode Pos</span></th>
                        <th class="px-4 py-4">Nama Penerima<br><span class="text-walnut-400 font-normal">No. Telepon</span></th>
                        <th class="px-4 py-4">Total Item<br><span class="text-walnut-400 font-normal">Total Bobot</span></th>
                        <th class="px-4 py-4">Total Ongkir<br><span class="text-walnut-400 font-normal">Nilai COD</span></th>
                        <th class="px-4 py-4">Status<br><span class="text-walnut-400 font-normal">Tanggal Terakhir Update</span></th>
                        <th class="px-4 py-4">Sumber Order</th>
                        <th class="px-4 py-4 text-center">Aksi Webhook</th>
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
                                'delivered' => 'bg-emerald-50 text-emerald-600 border border-emerald-200',
                                'returnintransit', 'return_in_transit' => 'bg-purple-50 text-purple-600 border border-purple-200',
                                'returned' => 'bg-rose-50 text-rose-600 border border-rose-200',
                                'dropping_off', 'droppingoff' => 'bg-blue-50 text-blue-600 border border-blue-200',
                                'picked' => 'bg-teal-50 text-teal-600 border border-teal-200',
                                'picking_up', 'pickingup' => 'bg-amber-50 text-amber-600 border border-amber-200',
                                'allocated' => 'bg-orange-50 text-orange-600 border border-orange-200',
                                'confirmed' => 'bg-gray-100 text-gray-700 border border-gray-200',
                                default => 'bg-gray-50 text-gray-600 border border-gray-200'
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
                            }
                        @endphp
                        <tr class="hover:bg-walnut-50/30 transition-colors group">
                            <td class="px-4 py-3 font-bold">
                                <span class="text-purple-700 underline decoration-dashed underline-offset-2">{{ $biteshipId }}</span><br>
                                <span class="text-walnut-500 text-[0.65rem]">{{ $order->order_code }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold border-b border-walnut-900 border-dashed pb-0.5">{{ $order->waybill_id ?? $order->shipment->tracking_number ?? 'Menunggu Resi' }}</span><br>
                                <span class="inline-block mt-1 px-2 py-0.5 border border-walnut-800/10 text-walnut-500 rounded-full text-[0.6rem] bg-white uppercase">{{ $order->shipment->courier ?? 'JNE' }} - {{ $order->shipment->service ?? 'REG' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold">{{ $order->created_at->format('d M Y') }}</span><br>
                                <span class="text-walnut-500 text-[0.65rem]">{{ $order->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold">{{ $address->district ?? $address->city ?? 'Jakarta' }}</span><br>
                                <span class="text-walnut-500 text-[0.65rem]">{{ $address->postal_code ?? '12345' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold">{{ $address->recipient_name ?? $order->user->name }}</span><br>
                                <span class="text-walnut-500 text-[0.65rem]">{{ $address->phone ?? $order->user->phone }}</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-walnut-900">
                                <span>{{ $qty }} Item</span><br>
                                <span class="text-walnut-500 font-normal text-[0.65rem]">{{ number_format($bobot, 2) }} kg</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-walnut-900">
                                <span>Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span><br>
                                <span class="inline-block mt-1 px-2 py-0.5 border border-walnut-800/20 text-walnut-500 rounded text-[0.6rem]">Non COD</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-full text-[0.65rem] font-bold {{ $statusPill }}">{{ $statusLabel }}</span><br>
                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 border border-walnut-800/10 text-walnut-500 rounded-full text-[0.6rem] bg-white">
                                    {{ ($order->shipment->updated_at ?? $order->updated_at)->format('d M Y, H:i') }} WIB
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($order->is_simulation)
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">Simulasi</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">API</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    @if(count($nextActions) > 0)
                                        <div class="flex flex-col gap-1 w-full max-w-[130px]">
                                            @php
                                                $firstTarget = array_key_first($nextActions);
                                            @endphp
                                            <form action="{{ route('simulasi.webhook.status') }}" method="POST" class="w-full">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $biteshipId }}">
                                                <input type="hidden" name="status" value="{{ $firstTarget }}">
                                                <button type="submit" class="w-full py-1 px-2 bg-purple-600 hover:bg-purple-700 text-white rounded text-[0.6rem] font-bold uppercase tracking-wider transition flex items-center justify-center gap-1 shadow-sm">
                                                    <i data-lucide="play" class="w-3 h-3"></i> Update Status
                                                </button>
                                            </form>
                                            
                                            @if(isset($nextActions['returnInTransit']))
                                            <form action="{{ route('simulasi.webhook.status') }}" method="POST" class="w-full mt-1">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $biteshipId }}">
                                                <input type="hidden" name="status" value="returnInTransit">
                                                <button type="submit" class="w-full py-1 px-2 bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 rounded text-[0.6rem] font-bold uppercase tracking-wider transition flex items-center justify-center gap-1">
                                                    <i data-lucide="corner-down-right" class="w-3 h-3"></i> Return Status
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[0.6rem] text-walnut-400 italic font-medium">Completed</span>
                                    @endif
                                    
                                    <button @click="openPriceModal('{{ $biteshipId }}', {{ $order->shipping_cost }})" class="w-full max-w-[130px] py-1 px-2 border border-gold-500 text-gold-600 bg-gold-50/30 hover:bg-gold-50 rounded text-[0.6rem] font-bold transition flex items-center justify-center gap-1 uppercase tracking-wider">
                                        <i data-lucide="dollar-sign" class="w-3 h-3"></i> Update Price
                                    </button>

                                    <button type="button" onclick="alert('Tags successfully simulated!')" class="w-full max-w-[130px] py-1 px-2 border border-purple-400 text-purple-600 bg-purple-50/30 hover:bg-purple-50 rounded text-[0.6rem] font-bold transition flex items-center justify-center gap-1 uppercase tracking-wider">
                                        <i data-lucide="tag" class="w-3 h-3"></i> Tambah Tags
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-walnut-500 font-medium text-sm">
                                Tidak ada pesanan untuk disimulasikan.
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
