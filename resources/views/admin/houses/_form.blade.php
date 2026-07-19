@csrf
@isset($house) @method('PUT') @endisset

<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm text-slate-600 mb-1.5">Blok <span class="text-slate-400 font-normal">(opsional)</span></label>
        <input type="text" name="block" value="{{ old('block', $house->block ?? '') }}" placeholder="Kosongkan kalau gak ada blok"
            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20">
        @error('block') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm text-slate-600 mb-1.5">Nomor rumah</label>
        <input type="text" name="house_number" value="{{ old('house_number', $house->house_number ?? '') }}" required
            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20">
        @error('house_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-1.5">Nama pemilik / kepala keluarga</label>
    <input type="text" name="owner_name" value="{{ old('owner_name', $house->owner_name ?? '') }}" required
        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20">
    @error('owner_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm text-slate-600 mb-1.5">No. HP</label>
        <input type="text" name="phone" value="{{ old('phone', $house->phone ?? '') }}"
            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20">
    </div>
    <div>
        <label class="block text-sm text-slate-600 mb-1.5">NIK pemilik</label>
        <input type="text" name="nik" value="{{ old('nik', $house->nik ?? '') }}" placeholder="16 digit NIK" maxlength="16" inputmode="numeric" required
            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600/20">
        @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mb-6">
    <label class="block text-sm text-slate-600 mb-1.5">Status IPL</label>
    <select name="ipl_status" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
        <option value="non_rukem" {{ old('ipl_status', $house->ipl_status ?? 'non_rukem') === 'non_rukem' ? 'selected' : '' }}>Non-Rukem</option>
        <option value="rukem" {{ old('ipl_status', $house->ipl_status ?? 'non_rukem') === 'rukem' ? 'selected' : '' }}>Rukem</option>
    </select>
    <p class="text-xs text-slate-400 mt-1">Menentukan besaran iuran bulanan. Diubah manual di sini gak otomatis kena biaya pendaftaran (biaya pendaftaran cuma berlaku pas warga daftar mandiri milih Rukem).</p>
</div>

<label class="flex items-center gap-2 text-sm text-slate-600 mb-6">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $house->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300">
    Rumah aktif
</label>

<div class="flex gap-3">
    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg">Simpan</button>
    <a href="{{ route('admin.houses.index') }}" class="text-slate-500 text-sm px-4 py-2.5">Batal</a>
</div>
