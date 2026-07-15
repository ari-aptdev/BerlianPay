@extends('layouts.resident')

@section('content')
<a href="{{ route('resident.dashboard') }}" class="text-sm text-slate-400 mb-4 inline-block">&larr; Kembali</a>

<h2 class="text-lg font-medium text-slate-900 mb-1">Konfirmasi Pembayaran</h2>
<p class="text-sm text-slate-500 mb-4">{{ $payment->house->fullLabel() }} &middot; IPL {{ $payment->periodLabel() }} &middot; Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>

@if ($errors->any())
    <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded-lg px-4 py-3 text-sm">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('resident.payments.confirm-submit', $payment) }}" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-xl p-5 space-y-4">
    @csrf

    <div>
        <label class="block text-sm text-slate-600 mb-1.5">Bukti transfer</label>
        <input type="file" name="proof_image" accept="image/*" capture="environment" required class="w-full text-sm">
        <p class="text-xs text-slate-400 mt-1">Foto/screenshot bukti transfer IPL bulan ini.</p>
    </div>

    <div>
        <label class="block text-sm text-slate-600 mb-1.5">Catatan (opsional)</label>
        <textarea name="notes" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm" placeholder="Mis. transfer dari rekening a.n. ..."></textarea>
    </div>

    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white text-sm py-2.5 rounded-lg">Kirim Konfirmasi</button>
</form>
@endsection
