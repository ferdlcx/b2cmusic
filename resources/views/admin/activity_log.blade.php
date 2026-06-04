@extends('admin.layouts.admin')

@section('title', 'Audit Log Aktivitas - Admin DjudasMS')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6">
        <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Laporan & Audit</span>
        <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Log Aktivitas</h1>
        <p class="text-xs text-slate-500 font-normal">Log audit aktivitas admin di dalam sistem panel kontrol.</p>
    </div>

    <!-- Table -->
    @if($logs->isEmpty())
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-12 text-center text-slate-500">
            <i data-lucide="history" class="w-10 h-10 text-slate-350 mx-auto mb-3"></i>
            <p class="text-xs font-semibold">Belum ada aktivitas tercatat.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3">Pengguna</th>
                        <th class="px-5 py-3">Tindakan</th>
                        <th class="px-5 py-3">Deskripsi</th>
                        <th class="px-5 py-3">Info IP & Browser</th>
                        <th class="px-5 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                            <td class="px-5 py-4">
                                @if($log->user)
                                    <span class="font-bold text-slate-900 block text-xs">{{ $log->user->name }}</span>
                                    <span class="text-[0.6rem] text-slate-400 block">{{ $log->user->email }}</span>
                                @else
                                    <span class="text-slate-400 font-semibold text-xs">Sistem</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-block text-[0.6rem] font-mono font-black uppercase bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-700 text-xs font-medium">
                                {{ $log->description }}
                                @if($log->model_type && $log->model_id)
                                    <span class="block text-[0.55rem] text-slate-400 font-mono mt-0.5">Model: {{ class_basename($log->model_type) }} #{{ $log->model_id }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 min-w-[150px]">
                                <span class="text-[0.65rem] text-slate-650 font-bold block">IP: {{ $log->ip_address }}</span>
                                <span class="text-[0.55rem] text-slate-400 font-medium line-clamp-1" title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-500 text-xs">
                                {{ $log->created_at->format('d M Y, H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pt-4">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
