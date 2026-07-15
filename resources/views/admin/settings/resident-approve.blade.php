@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-2">Setujui akun warga</h2>
<p class="text-sm text-slate-500 mb-6">{{ $resident->name }} &middot; NIK {{ $resident->nik }} &middot; {{ $resident->phone }}</p>

<div class="bg-white rounded-xl border border-slate-200 p-6 max-w-xl">
    <form method="POST" action="{{ route('admin.residents.approve', $resident) }}">
        @csrf

        <div class="mb-6">
            <label class="block text-sm text-slate-600 mb-1.5">Assign ke rumah</label>
            @if (count($assignedHouseIds))
                <p class="text-xs text-green-600 mb-2"><i class="ti ti-check"></i> Rumah otomatis kedeteksi lewat NIK, cek lagi kalau perlu.</p>
            @else
                <p class="text-xs text-amber-600 mb-2"><i class="ti ti-alert-circle"></i> Belum ada rumah yang cocok otomatis lewat NIK, pilih manual di bawah.</p>
            @endif
            <div class="border border-slate-200 rounded-lg p-3 max-h-60 overflow-y-auto space-y-1.5">
                @foreach ($houses as $house)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="house_ids[]" value="{{ $house->id }}" {{ in_array($house->id, $assignedHouseIds) ? 'checked' : '' }} class="rounded border-slate-300">
                        {{ $house->fullLabel() }} - {{ $house->owner_name }} ({{ $house->nik }})
                    </label>
                @endforeach
            </div>
            @error('house_ids') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg">Setujui & Aktifkan Akun</button>
            <a href="{{ route('admin.residents.index') }}" class="text-slate-500 text-sm px-4 py-2.5">Batal</a>
        </div>
    </form>
</div>
@endsection
