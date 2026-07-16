@csrf
@isset($payment) @method('PUT') @endisset

<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-1.5">Rumah</label>
    <select name="house_id" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
        <option value="">Pilih rumah</option>
        @foreach ($houses as $house)
            <option value="{{ $house->id }}" @selected(old('house_id', $payment->house_id ?? null) == $house->id)>
                {{ $house->fullLabel() }} - {{ $house->owner_name }} ({{ $house->isRukem() ? 'Rukem' : 'Non-Rukem' }})
            </option>
        @endforeach
    </select>
    <p class="text-xs text-slate-400 mt-1">Tarif otomatis mengikuti status Rukem/Non-Rukem rumah ini, tapi nominal di bawah tetap bisa diedit manual.</p>
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

<div class="mb-6">
    <label class="block text-sm text-slate-600 mb-1.5">TTD bukti serah terima</label>
    <p class="text-xs text-slate-400 mb-2">Diisi warga langsung di layar HP/tablet saat bendahara keliling menagih IPL. Ini jadi bukti serah terima buat warga & pengurus.</p>

    @isset($payment)
        @if ($payment->signature)
            <div class="mb-2 border border-slate-200 rounded-lg p-2 bg-white inline-block">
                <img src="{{ $payment->signature }}" alt="TTD tersimpan" class="h-20">
                <p class="text-xs text-slate-400 mt-1">TTD tersimpan ({{ $payment->signed_at?->format('d-m-Y H:i') }}). Gambar ulang di bawah kalau perlu ganti.</p>
            </div>
        @endif
    @endisset

    <div class="border border-slate-200 rounded-lg overflow-hidden bg-white touch-none">
        <canvas id="signaturePad" class="w-full" height="150" style="cursor: crosshair;"></canvas>
    </div>
    <div class="flex gap-2 mt-2">
        <button type="button" onclick="clearSignature()" class="text-xs text-slate-500 border border-slate-200 px-3 py-1.5 rounded-lg">Bersihkan</button>
    </div>
    <input type="hidden" name="signature" id="signatureInput">
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg">Simpan</button>
    <a href="{{ route('admin.payments.index') }}" class="text-slate-500 text-sm px-4 py-2.5">Batal</a>
</div>

<script>
(function () {
    const canvas = document.getElementById('signaturePad');
    const ctx = canvas.getContext('2d');
    const input = document.getElementById('signatureInput');
    let drawing = false;
    let hasDrawn = false;

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = 150 * ratio;
        ctx.scale(ratio, ratio);
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#1e293b';
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const point = e.touches ? e.touches[0] : e;
        return { x: point.clientX - rect.left, y: point.clientY - rect.top };
    }

    function start(e) {
        drawing = true;
        hasDrawn = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
        e.preventDefault();
    }

    function move(e) {
        if (!drawing) return;
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        e.preventDefault();
    }

    function end() {
        if (!drawing) return;
        drawing = false;
        input.value = canvas.toDataURL('image/png');
    }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    canvas.addEventListener('mouseup', end);
    canvas.addEventListener('mouseleave', end);
    canvas.addEventListener('touchstart', start);
    canvas.addEventListener('touchmove', move);
    canvas.addEventListener('touchend', end);

    window.clearSignature = function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        input.value = '';
        hasDrawn = false;
    };
})();
</script>
