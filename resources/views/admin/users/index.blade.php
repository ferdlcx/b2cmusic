@extends('admin.layouts.admin')

@section('title', 'Kelola Pengguna - Admin MusicStore Luxe')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-slate-100 pb-6">
        <span class="text-xs uppercase tracking-[0.45em] text-slate-500 font-bold">Interaksi Sistem</span>
        <h1 class="text-3xl font-black uppercase tracking-tight text-slate-950 mt-2">Kelola Pengguna</h1>
        <p class="text-xs text-slate-500">Lihat detail pelanggan, riwayat pesanan mereka, dan kelola status akses akun.</p>
    </div>

    <!-- Table -->
    @if($users->isEmpty())
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-12 text-center text-slate-500">
            <i data-lucide="users" class="w-10 h-10 text-slate-350 mx-auto mb-3"></i>
            <p class="text-xs font-semibold">Belum ada pengguna terdaftar.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-[32px] border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase tracking-widest text-slate-400 bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Bergabung</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/50">
                            <td class="px-6 py-4 flex items-center gap-4">
                                <div class="w-10 h-10 bg-indigo-50 border border-indigo-100 rounded-full flex items-center justify-center font-display font-black text-xs text-indigo-650 uppercase shrink-0 overflow-hidden">
                                    @if($user->profile_photo)
                                        <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                                    @else
                                        {{ substr($user->name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $user->name }}</span>
                                    <span class="text-[0.65rem] text-slate-450 font-semibold">{{ $user->email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold uppercase tracking-wider">
                                @if($user->role === 'admin' || $user->role === 'super_admin')
                                    <span class="text-indigo-600 font-black">Admin</span>
                                @else
                                    <span class="text-slate-555">Customer</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $userStatus = strtolower($user->status);
                                    $isActive = $userStatus === 'active' || $user->status == '1';
                                @endphp
                                @if($isActive)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold bg-emerald-50 text-emerald-700 border border-emerald-250 uppercase">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold bg-rose-50 text-rose-700 border border-rose-200 uppercase">Ditangguhkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-xs font-bold text-slate-650 hover:underline">Lihat</a>
                                
                                @if($user->role !== 'admin' && $user->role !== 'super_admin')
                                    <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold {{ $isActive ? 'text-rose-600' : 'text-emerald-600' }} hover:underline">
                                            {{ $isActive ? 'Tangguhkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pt-4">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
