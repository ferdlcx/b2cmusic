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
        <form action="{{ route('simulasi.clear') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus seluruh riwayat pesanan dari database lokal dan Biteship? Data yang dihapus tidak bisa dikembalikan.');">
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
                <thead class="bg-white border-b border-walnut-800/10 text-walnut-600 text-[0.7rem] font-bold">
                    <tr>
                        <th class="px-4 py-4">Order ID<br><span class="text-walnut-400 font-normal">Reference ID</span></th>
                        <th class="px-4 py-4">Nomor Resi<br><span class="text-walnut-400 font-normal">Kurir - Layanan</span></th>
                        <th class="px-4 py-4">Tanggal Dibuat<br><span class="text-walnut-400 font-normal">Jam Dibuat</span></th>
                        <th class="px-4 py-4">Kec. Tujuan<br><span class="text-walnut-400 font-normal">Kode Pos</span></th>
                        <th class="px-4 py-4">Nama Penerima<br><span class="text-walnut-400 font-normal">No. Telepon</span></th>
                        <th class="px-4 py-4">Total Item<br><span class="text-walnut-400 font-normal">Total Bobot (kg)</span></th>
                        <th class="px-4 py-4">Total Ongkir<br><span class="text-walnut-400 font-normal">Nilai COD</span></th>
                        <th class="px-4 py-4">Status<br><span class="text-walnut-400 font-normal">Tanggal Terakhir Update</span></th>
                        <th class="px-4 py-4 text-center">Aksi<br><span class="text-walnut-400 font-normal">Webhook</span></th>
                        <th class="px-4 py-4">Tag<br><span class="text-walnut-400 font-normal">Sumber Order</span></th>
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
                                'delivered' => 'bg-emerald-50 text-emerald-600',
                                'cancelled', 'rejected' => 'bg-red-50 text-red-600',
                                'dropping_off' => 'bg-blue-100 text-blue-700',
                                'in_transit' => 'bg-blue-50 text-blue-600',
                                'picked' => 'bg-teal-50 text-teal-600',
                                'picking_up' => 'bg-amber-50 text-amber-600',
                                'allocated' => 'bg-orange-50 text-orange-600',
                                'confirmed' => 'bg-gray-100 text-gray-700',
                                default => 'bg-gray-50 text-gray-600'
                            };

                            $statusLabel = match(strtolower($currentStatus)) {
                                'delivered' => 'Berhasil Dikirim',
                                'cancelled' => 'Dibatalkan',
                                'dropping_off' => 'Dalam Pengantaran',
                                'in_transit' => 'Dalam Perjalanan',
                                'picked' => 'Barang Dijemput',
                                'picking_up' => 'Menuju Lokasi Penjemputan',
                                'allocated' => 'Alokasi',
                                'confirmed' => 'Terkonfirmasi',
                                'processing' => 'Diproses',
                                'shipped' => 'Dikirim',
                                'pending' => 'Pending',
                                default => ucfirst($currentStatus)
                            };

                            $nextStatuses = [];
                            $cs = strtolower($currentStatus);
                            if (in_array($cs, ['pending', 'processing', 'confirmed'])) {
                                $nextStatuses = [
                                    'courier_not_found' => 'Kurir Tidak Ketemu',
                                    'allocated' => 'Alocate',
                                    'cancelled' => 'Cancel'
                                ];
                            } elseif ($cs === 'allocated') {
                                $nextStatuses = ['picking_up' => 'Picking Up'];
                            } elseif ($cs === 'picking_up') {
                                $nextStatuses = ['picked' => 'Barang Dijemput', 'on_hold' => 'Ditahan'];
                            } elseif ($cs === 'picked') {
                                $nextStatuses = ['in_transit' => 'Dalam Perjalanan', 'dropping_off' => 'Menuju Pelanggan'];
                            } elseif ($cs === 'in_transit') {
                                $nextStatuses = ['dropping_off' => 'Menuju Pelanggan'];
                            } elseif ($cs === 'dropping_off' || $cs === 'shipped') {
                                $nextStatuses = [
                                    'delivered' => 'Selesai',
                                    'on_hold' => 'Ditahan',
                                    'return_in_transit' => 'Return Process',
                                    'disposed' => 'Hancurkan Paket'
                                ];
                            } elseif ($cs === 'on_hold') {
                                $nextStatuses = [
                                    'delivered' => 'Selesai', 
                                    'rejected' => 'Rejected', 
                                    'return_in_transit' => 'Return Process', 
                                    'disposed' => 'Hancurkan Paket'
                                ];
                            } elseif ($cs === 'return_in_transit') {
                                $nextStatuses = ['returned' => 'Returned'];
                            }
                        @endphp
                        <tr class="hover:bg-walnut-50/30 transition-colors group">
                            <td class="px-4 py-3">
                                <span class="font-bold text-purple-700 underline decoration-dashed underline-offset-2 cursor-pointer">{{ $biteshipId }}</span><br>
                                <span class="text-walnut-500 text-[0.65rem]">-</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold border-b border-walnut-900 border-dashed pb-0.5 cursor-pointer">{{ $order->waybill_id ?? $order->shipment->tracking_number ?? 'Menunggu Resi' }}</span><br>
                                <span class="inline-block mt-1 px-2 py-0.5 border border-walnut-800/10 text-walnut-500 rounded-full text-[0.6rem] bg-white">{{ strtoupper($order->shipment->courier ?? 'JNE') }} - {{ strtoupper($order->shipment->service ?? 'REG') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold">{{ $order->created_at->format('d M Y') }}</span><br>
                                <span class="text-walnut-500 text-[0.65rem]">{{ $order->created_at->format('H.i') }} WIB</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold border-b border-walnut-900 border-dashed pb-0.5 cursor-pointer">{{ $address->city ?? 'Jakarta' }}</span><br>
                                <span class="text-walnut-500 text-[0.65rem]">{{ $address->postal_code ?? '12345' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold">{{ $address->recipient_name ?? $order->user->name }}</span><br>
                                <span class="text-walnut-500 text-[0.65rem]">{{ $address->phone ?? $order->user->phone }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold border-b border-walnut-900 border-dashed pb-0.5 cursor-pointer">{{ $qty }} Item</span><br>
                                <span class="text-walnut-500 text-[0.65rem]">{{ number_format($bobot, 2) }} kg</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold border-b border-walnut-900 border-dashed pb-0.5 cursor-pointer">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span><br>
                                <span class="inline-block mt-1 px-2 py-0.5 border border-walnut-800/20 text-walnut-500 rounded text-[0.6rem]">Non COD</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-full text-[0.65rem] font-bold {{ $statusPill }}">{{ $statusLabel }}</span><br>
                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 border border-walnut-800/10 text-walnut-500 rounded-full text-[0.6rem] bg-white">
                                    {{ ($order->shipment->updated_at ?? $order->updated_at)->format('d M Y') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-col items-center gap-1.5 relative">
                                    @if(count($nextStatuses) > 0)
                                        <button @click="openDropdown('{{ $biteshipId }}')" class="inline-flex items-center justify-center w-[110px] px-2 py-1 border border-gold-500 text-gold-600 bg-gold-50/30 hover:bg-gold-50 rounded-lg text-[0.6rem] font-bold transition">
                                            <span class="flex items-center gap-1"><i data-lucide="file-text" class="w-3 h-3"></i> Update Status</span>
                                        </button>
                                        
                                        <!-- Dropdown Menu for Status -->
                                        <div x-show="activeDropdown === '{{ $biteshipId }}'" @click.away="activeDropdown = null" style="display: none;" class="absolute top-8 left-1/2 -translate-x-1/2 z-50 w-48 bg-white border border-walnut-800/10 shadow-xl rounded-xl py-2">
                                            <div class="max-h-60 overflow-y-auto">
                                                @foreach($nextStatuses as $val => $label)
                                                    <form action="{{ route('simulasi.webhook.status') }}" method="POST" class="w-full">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{ $biteshipId }}">
                                                        <input type="hidden" name="status" value="{{ $val }}">
                                                        <button type="submit" class="w-full text-left px-4 py-2 hover:bg-purple-50 text-[0.65rem] font-bold text-purple-700 uppercase flex items-center gap-2 transition">
                                                            <i data-lucide="truck" class="w-3 h-3"></i> {{ $label }}
                                                        </button>
                                                    </form>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-[0.6rem] text-walnut-400 italic">No Actions</span>
                                    @endif
                                    
                                    <button @click="openPriceModal('{{ $biteshipId }}', {{ $order->shipping_cost }})" class="inline-flex items-center justify-center w-[110px] px-2 py-1 border border-gold-500 text-gold-600 bg-gold-50/30 hover:bg-gold-50 rounded-lg text-[0.6rem] font-bold transition">
                                        <span class="flex items-center gap-1"><i data-lucide="dollar-sign" class="w-3 h-3"></i> Update Price</span>
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 text-[0.65rem] font-bold text-purple-700 cursor-pointer">
                                    <i data-lucide="tag" class="w-3 h-3 fill-purple-700"></i> Tambah Tags
                                </span><br>
                                <span class="inline-block mt-1 px-2 py-0.5 border border-walnut-800/10 text-walnut-500 rounded-full text-[0.6rem] bg-white">API</span>
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
            activeDropdown: null,
            priceModalOpen: false,
            selectedOrderId: '',
            selectedPrice: 0,
            
            openDropdown(id) {
                if (this.activeDropdown === id) {
                    this.activeDropdown = null;
                } else {
                    this.activeDropdown = id;
                }
            },
            
            openPriceModal(id, currentPrice) {
                this.selectedOrderId = id;
                this.selectedPrice = currentPrice;
                this.priceModalOpen = true;
                this.activeDropdown = null;
            }
        }
    }
</script>
@endsection
