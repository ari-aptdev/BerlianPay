@csrf
@isset($payment) @method('PUT') @endisset

<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-1.5">Rumah</label>
    <select name="house_id" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
        <option value="">Pilih rumah</option>
        @foreach ($houses as $house)
            <option value="{{ $house->id }}" @selected(old('house_id', $payment->house_id ?? null) == $house->id)>
                {{ $house->fullLabel() }} - {{ $house->owner_name }}
            </option>
        @endforeach
    </select>
    @error('house_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm text-slate-600 mb-1.5">Bulan</label>
        <select name="period_month" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" @selected(old('period_month', $payment->period_month ?? now()->month) == $m)>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endfor
        </select>
    </div>
    <div>
        <label class="block text-sm text-slate-600 mb-1.5">Tahun</label>
        <input type="number" name="period_year" value="{{ old('period_year', $payment->period_year ?? now()->year) }}" required
            class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
    </div>
</div>

<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-1.5">Nominal (Rp)</label>
    <input type="number" name="amount" value="{{ old('amount', $payment->amount ?? '') }}" required
        class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
    @error('amount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-1.5">Status</label>
    <select name="status" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
        <option value="paid" @selected(old('status', $payment->status ?? '') === 'paid')>Lunas</option>
        <option value="unpaid" @selected(old('status', $payment->status ?? '') === 'unpaid')>Belum bayar</option>
    </select>
</div>

<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-1.5">Bukti transfer (opsional)</label>
    <input type="file" name="proof_image" accept="image/*" class="w-full text-sm">
    @error('proof_image') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    @isset($payment)
        @if ($payment->proof_image)
            <a href="{{ Storage::url($payment->proof_image) }}" target="_blank" class="text-xs text-brand-600 hover:underline mt-1 inline-block">Lihat bukti saat ini</a>
        @endif
    @endisset
</div>

<div class="mb-6">
    <label class="block text-sm text-slate-600 mb-1.5">Catatan (opsional)</label>
    <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">{{ old('notes', $payment->notes ?? '') }}</textarea>
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg">Simpan</button>
    <a href="{{ route('admin.payments.index') }}" class="text-slate-500 text-sm px-4 py-2.5">Batal</a>
</div>
