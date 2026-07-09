@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-medium text-slate-900">Data warga & rumah</h2>
    <a href="{{ route('admin.houses.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">
        <i class="ti ti-plus"></i> Tambah rumah
    </a>
</div>

<form method="GET" class="mb-4">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau nomor rumah..."
        class="w-full sm:w-80 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20">
</form>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Blok/No.</th>
                <th class="text-left px-4 py-2 font-normal">Nama pemilik</th>
                <th class="text-left px-4 py-2 font-normal">Kontak</th>
                <th class="text-left px-4 py-2 font-normal">NIK</th>
                <th class="text-left px-4 py-2 font-normal">Status</th>
                <th class="text-right px-4 py-2 font-normal">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($houses as $house)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">{{ $house->fullLabel() }}</td>
                    <td class="px-4 py-2.5">{{ $house->owner_name }}</td>
                    <td class="px-4 py-2.5">{{ $house->phone ?? '-' }}</td>
                    <td class="px-4 py-2.5">{{ $house->nik }}</td>
                    <td class="px-4 py-2.5">
                        @if ($house->is_active)
                            <span class="bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-md">Aktif</span>
                        @else
                            <span class="bg-slate-100 text-slate-500 text-xs px-2.5 py-1 rounded-md">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-right space-x-2">
                        <a href="{{ route('admin.houses.edit', $house) }}" class="text-brand-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.houses.destroy', $house) }}" class="inline" onsubmit="return confirm('Nonaktifkan rumah ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline">Nonaktifkan</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada data rumah.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $houses->links() }}</div>
@endsection
