@csrf
@isset($iplRate) @method('PUT') @endisset

<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-1.5">Tipe rumah</label>
    <input type="text" name="house_type" value="{{ old('house_type', $iplRate->house_type ?? '') }}" placeholder="mis. Tipe 36" required
        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20">
    @error('house_type') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-1.5">Nominal per bulan (Rp)</label>
    <input type="number" name="nominal" value="{{ old('nominal', $iplRate->nominal ?? '') }}" required
        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20">
    @error('nominal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-6">
    <label class="block text-sm text-slate-600 mb-1.5">Berlaku sejak</label>
    <input type="date" name="effective_date" value="{{ old('effective_date', isset($iplRate) ? $iplRate->effective_date->format('Y-m-d') : '') }}" required
        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20">
    @error('effective_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg">Simpan</button>
    <a href="{{ route('admin.ipl-rates.index') }}" class="text-slate-500 text-sm px-4 py-2.5">Batal</a>
</div>
