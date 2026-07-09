@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-medium text-slate-900">Akun warga</h2>
    <a href="{{ route('admin.residents.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">
        <i class="ti ti-plus"></i> Buat akun warga
    </a>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
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
                        @if ($resident->is_active)
                            <span class="bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-md">Aktif</span>
                        @else
                            <span class="bg-slate-100 text-slate-500 text-xs px-2.5 py-1 rounded-md">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-right space-x-2">
                        <form method="POST" action="{{ route('admin.residents.toggle-active', $resident) }}" class="inline">
                            @csrf @method('PATCH')
                            <button class="text-brand-600 hover:underline">{{ $resident->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.residents.reset-password', $resident) }}" class="inline" onsubmit="return confirm('Reset password warga ini?')">
                            @csrf @method('PATCH')
                            <button class="text-slate-500 hover:underline">Reset password</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Belum ada akun warga.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $residents->links() }}</div>
@endsection
