@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-medium text-slate-900">Tarif IPL</h2>
    <a href="{{ route('admin.ipl-rates.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">
        <i class="ti ti-plus"></i> Tambah tarif
    </a>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Tipe rumah</th>
                <th class="text-left px-4 py-2 font-normal">Nominal</th>
                <th class="text-left px-4 py-2 font-normal">Berlaku sejak</th>
                <th class="text-right px-4 py-2 font-normal">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rates as $rate)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">{{ $rate->house_type }}</td>
                    <td class="px-4 py-2.5">Rp {{ number_format($rate->nominal, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5">{{ $rate->effective_date->translatedFormat('d F Y') }}</td>
                    <td class="px-4 py-2.5 text-right space-x-2">
                        <a href="{{ route('admin.ipl-rates.edit', $rate) }}" class="text-brand-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.ipl-rates.destroy', $rate) }}" class="inline" onsubmit="return confirm('Hapus tarif ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Belum ada tarif IPL.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $rates->links() }}</div>
@endsection
