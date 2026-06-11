@extends('layouts.app')

@section('content')
<div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-tight text-walnut-950 flex items-center gap-2">
                <i data-lucide="globe" class="w-6 h-6 text-gold-600"></i> API Status Dashboard
            </h1>
            <p class="text-[0.7rem] font-bold text-muted mt-1">Cek Ketersediaan dan Kuota Layanan API Eksternal (RajaOngkir, Biteship, MailerSend)</p>
        </div>
        <a href="{{ route('simulasi.index') }}" class="px-4 py-2 bg-walnut-100 hover:bg-walnut-200 text-walnut-800 rounded-xl text-xs font-bold uppercase tracking-widest transition flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Simulator
        </a>
    </div>

    <div class="bg-white border border-walnut-800/10 shadow-sm overflow-hidden rounded-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-cream-50 text-walnut-600 text-[0.65rem] tracking-widest font-bold">
                    <tr>
                        <th class="px-6 py-4">Layanan API</th>
                        <th class="px-6 py-4">Status / Availability</th>
                        <th class="px-6 py-4">HTTP Code</th>
                        <th class="px-6 py-4">Response Time</th>
                        <th class="px-6 py-4">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-walnut-800/5 text-walnut-950 font-medium text-xs">
                    @foreach ($results as $name => $data)
                        @php
                            $isOk = str_contains($data['status'], 'OK');
                            $isSkip = str_contains($data['status'], 'SKIPPED');
                            $statusColor = $isOk ? 'text-emerald-600 bg-emerald-50' : ($isSkip ? 'text-walnut-500 bg-walnut-50' : 'text-red-600 bg-red-50');
                            $icon = $isOk ? 'check-circle' : ($isSkip ? 'minus-circle' : 'alert-circle');
                        @endphp
                        <tr class="hover:bg-walnut-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-black text-sm text-walnut-900 flex items-center gap-2">
                                    <i data-lucide="server" class="w-4 h-4 text-walnut-400"></i> {{ $name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-widest {{ $statusColor }}">
                                    <i data-lucide="{{ $icon }}" class="w-3.5 h-3.5"></i> {{ $data['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <code class="px-2 py-1 bg-cream-50 border border-walnut-800/10 rounded text-walnut-700 font-mono text-xs">{{ $data['code'] }}</code>
                            </td>
                            <td class="px-6 py-4 font-mono text-walnut-600">
                                {{ $data['time'] }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-walnut-500 font-normal truncate max-w-xs block" title="{{ $data['detail'] }}">
                                    {{ Str::limit($data['detail'], 60) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
