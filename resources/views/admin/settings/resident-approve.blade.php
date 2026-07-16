@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-2">Setujui akun warga</h2>
<p class="text-sm text-slate-500 mb-6">{{ $resident->name }} &middot; NIK {{ $resident->nik }} &middot; {{ $resident->phone }}</p>

<div class="bg-white rounded-xl border border-slate-200 p-6 max-w-lg">
    <div class="bg-brand-50 text-brand-700 text-xs rounded-lg px-3 py-2 mb-4">
        Data di bawah ini diisi warga sendiri pas daftar. Cek lagi sebelum di-approve — data rumah baru akan benar-benar
        dibuat di "Data Warga & Rumah" setelah kamu klik Setujui.
    </div>

    <form method="POST" action="{{ route('admin.residents.approve', $resident) }}">
        @csrf

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">Blok</label>
                <input type="text" name="block" value="{{ old('block', $resident->pending_block) }}" required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
                @error('block') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">No. Rumah</label>
                <input type="text" name="house_number" value="{{ old('house_number', $resident->pending_house_number) }}" required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
                @error('house_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm text-slate-600 mb-2">Status IPL</label>
            <div class="space-y-2">
                <label class="flex items-center gap-3 border border-slate-200 rounded-lg p-3 cursor-pointer">
                    <input type="radio" name="ipl_status" value="non_rukem" {{ old('ipl_status', $resident->pending_wants_rukem ? 'rukem' : 'non_rukem') === 'non_rukem' ? 'checked' : '' }}>
                    <span class="text-sm text-slate-700">Non-Rukem</span>
                </label>
                <label class="flex items-center gap-3 border border-slate-200 rounded-lg p-3 cursor-pointer">
                    <input type="radio" name="ipl_status" value="rukem" {{ old('ipl_status', $resident->pending_wants_rukem ? 'rukem' : 'non_rukem') === 'rukem' ? 'checked' : '' }}>
                    <span class="text-sm text-slate-700">Rukem</span>
                </label>
            </div>
            @if ($resident->pending_wants_rukem)
                <p class="text-xs text-amber-600 mt-2"><i class="ti ti-info-circle"></i> Warga ini pilih ikut Rukem pas daftar — kalau disetujui sebagai Rukem, tagihan biaya pendaftaran otomatis dibuat.</p>
            @endif
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg">Setujui & Aktifkan Akun</button>
            <a href="{{ route('admin.residents.index') }}" class="text-slate-500 text-sm px-4 py-2.5">Batal</a>
        </div>
    </form>
</div>
@endsection
