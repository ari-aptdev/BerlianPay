@extends('layouts.resident')

@section('content')
<a href="{{ route('resident.dashboard') }}" class="text-sm text-slate-400 mb-4 inline-block">&larr; Kembali</a>

<h2 class="text-lg font-medium text-slate-900 mb-1">Konfirmasi Pembayaran</h2>
<p class="text-sm text-slate-500 mb-4">{{ $payment->house->fullLabel() }} &middot; {{ $payment->displayLabel() }}</p>

<div class="bg-white border border-slate-200 rounded-xl p-4 mb-4">
    <div class="flex items-center justify-between mb-3">
        <p class="text-sm font-medium text-slate-700">Rincian yang harus dibayar</p>
        @if (! $payment->isRegistrationFee())
            <span class="bg-brand-50 text-brand-700 text-xs px-2 py-1 rounded-md">{{ $payment->house->isRukem() ? 'Rukem' : 'Non-Rukem' }}</span>
        @endif
    </div>
    @foreach ($payment->resolvedBreakdown() as $label => $amount)
        <div class="flex justify-between text-sm py-1.5 border-t border-slate-100">
            <span class="text-slate-500">{{ $label }}</span>
            <span class="text-slate-700">Rp {{ number_format($amount, 0, ',', '.') }}</span>
        </div>
    @endforeach
    <div class="flex justify-between text-sm font-semibold pt-2 mt-1 border-t border-slate-200">
        <span>Total</span>
        <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
    </div>
</div>

@if ($errors->any())
    <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded-lg px-4 py-3 text-sm">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('resident.payments.confirm-submit', $payment) }}" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-xl p-5 space-y-4">
    @csrf

    <div>
        <label class="block text-sm text-slate-600 mb-1.5">Bukti transfer <span class="text-red-500">*</span></label>
        <input type="file" name="proof_image" accept="image/*" required class="w-full text-sm">
        <p class="text-xs text-slate-400 mt-1">Wajib diisi. Bisa pilih foto dari galeri atau ambil foto baru.</p>
    </div>

    <div>
        <label class="block text-sm text-slate-600 mb-1.5">Catatan (opsional)</label>
        <textarea name="notes" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm" placeholder="Mis. transfer dari rekening a.n. ..."></textarea>
    </div>

    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white text-base font-semibold py-3.5 rounded-xl">Kirim Konfirmasi</button>
</form>
@endsection
