@extends('admin.layouts.admin')

@section('title', 'Kelola Pengguna - Admin DjudasMS')

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="border-b border-walnut-800/10 pb-6">
        <span class="text-xs uppercase tracking-[0.45em] text-gold-600 font-bold">Interaksi Sistem</span>
        <h1 class="font-display text-3xl font-black uppercase tracking-tight text-walnut-950 mt-2">Kelola <span class="text-gold-500">Pengguna</span></h1>
        <p class="text-xs text-muted mt-2 font-medium">Lihat detail pelanggan, riwayat pesanan mereka, dan kelola status akses akun.</p>
    </div>

    <!-- Table -->
    @if($users->isEmpty())
        <div class="bg-cream-50 border border-walnut-800/10 p-12 text-center text-muted">
            <i data-lucide="users" class="w-10 h-10 text-gold-500 mx-auto mb-3 opacity-50"></i>
            <p class="text-[0.75rem] font-bold uppercase tracking-widest text-walnut-950">Belum ada pengguna terdaftar.</p>
        </div>
    @else
        <div class="overflow-hidden border border-walnut-800/10 bg-cream-50">
            <table class="w-full text-sm text-left">
                <thead class="text-[0.65rem] uppercase tracking-widest text-walnut-800 font-bold border-b border-walnut-800/10 bg-walnut-800/5">
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
                        <tr class="border-b border-walnut-800/5 last:border-0 hover:bg-walnut-800/5 transition">
                            <td class="px-6 py-4 flex items-center gap-4">
                                <div class="w-10 h-10 border border-gold-500 bg-gold-500/10 rounded-full flex items-center justify-center font-display font-black text-xs text-gold-600 uppercase shrink-0 overflow-hidden">
                                    @if($user->profile_photo)
                                        <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                                    @else
                                        {{ substr($user->name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <span class="font-bold uppercase tracking-widest text-walnut-950 block text-[0.75rem]">{{ $user->name }}</span>
                                    <span class="text-[0.65rem] text-muted font-bold">{{ $user->email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[0.65rem] font-bold uppercase tracking-widest">
                                @if($user->role === 'admin' || $user->role === 'super_admin')
                                    <span class="bg-walnut-900 text-gold-500 px-2 py-0.5">Admin</span>
                                @else
                                    <span class="text-muted">Customer</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $userStatus = strtolower($user->status);
                                    $isActive = $userStatus === 'active' || $user->status == '1';
                                @endphp
                                @if($isActive)
                                    <span class="inline-flex items-center px-2 py-0.5 text-[0.65rem] font-bold border border-green-600/30 text-green-600 uppercase tracking-wider bg-green-50">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-[0.65rem] font-bold border border-red-600/30 text-red-600 uppercase tracking-wider bg-red-50">Ditangguhkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-muted text-[0.7rem] font-bold uppercase tracking-widest">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-[0.65rem] font-bold uppercase tracking-widest text-walnut-900 hover:text-gold-600 transition">Lihat</a>
                                
                                @if($user->role !== 'admin' && $user->role !== 'super_admin')
                                    <form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-[0.65rem] font-bold uppercase tracking-widest {{ $isActive ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} transition">
                                            {{ $isActive ? 'Tangguhkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini secara permanen? Semua data terkait pengguna ini akan hilang.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[0.65rem] font-bold uppercase tracking-widest text-red-600 hover:text-red-800 transition">
                                            Hapus
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
