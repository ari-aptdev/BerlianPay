@extends('layouts.admin')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <h2 class="text-lg font-medium text-slate-900">Akun warga</h2>
    @if (auth()->user()->canAccess('residents', 'edit'))
        <a href="{{ route('admin.residents.create') }}" class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg w-full sm:w-auto">
            <i class="ti ti-plus"></i> Buat akun warga
        </a>
    @endif
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Nama</th>
                <th class="text-left px-4 py-2 font-normal">Username login</th>
                <th class="text-left px-4 py-2 font-normal">Rumah</th>
                <th class="text-left px-4 py-2 font-normal">Status</th>
                <th class="text-right px-4 py-2 font-normal">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($residents as $resident)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">{{ $resident->name }}</td>
                    <td class="px-4 py-2.5 font-mono text-xs bg-slate-50 rounded w-fit">{{ $resident->username }}</td>
                    <td class="px-4 py-2.5">{{ $resident->houses->map->fullLabel()->join(', ') ?: '-' }}</td>
                    <td class="px-4 py-2.5">
                        @if (! $resident->is_active && $resident->houses->isEmpty())
                            <span class="bg-amber-50 text-amber-700 text-xs px-2.5 py-1 rounded-md">Menunggu Approval</span>
                        @elseif ($resident->is_active)
                            <span class="bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-md">Aktif</span>
                        @else
                            <span class="bg-slate-100 text-slate-500 text-xs px-2.5 py-1 rounded-md">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-right space-x-2">
                        @if (auth()->user()->canAccess('residents', 'edit'))
                            @if (! $resident->is_active && $resident->houses->isEmpty())
                                <a href="{{ route('admin.residents.approve-form', $resident) }}" class="text-brand-600 hover:underline font-medium">Setujui</a>
                            @else
                                <form method="POST" action="{{ route('admin.residents.toggle-active', $resident) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button class="text-brand-600 hover:underline">{{ $resident->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </form>
                            @endif
                            <button type="button" onclick="document.getElementById('reset-pw-{{ $resident->id }}').classList.toggle('hidden')" class="text-slate-500 hover:underline">Reset password</button>
                        @else
                            <span class="text-slate-300 text-xs">Lihat saja</span>
                        @endif
                    </td>
                </tr>
                @if (auth()->user()->canAccess('residents', 'edit'))
                <tr id="reset-pw-{{ $resident->id }}" class="hidden border-t border-slate-100 bg-slate-50">
                    <td colspan="5" class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.residents.reset-password', $resident) }}" class="flex flex-wrap items-center gap-2">
                            @csrf @method('PATCH')
                            <label class="text-xs text-slate-500">Password baru buat {{ $resident->name }}:</label>
                            <input type="text" name="new_password" minlength="6" required placeholder="Minimal 6 karakter" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm">
                            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-xs px-3 py-1.5 rounded-lg">Simpan Password</button>
                        </form>
                    </td>
                </tr>
                @endif
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Belum ada akun warga.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $residents->links() }}</div>
@endsection
