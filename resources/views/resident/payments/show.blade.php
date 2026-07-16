@extends('layouts.resident')

@section('content')
<a href="{{ route('resident.payments.index') }}" class="text-sm text-slate-400 mb-4 inline-block">&larr; Kembali</a>

<div class="bg-white border border-slate-200 rounded-xl p-5">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-medium text-slate-900">Kwitansi IPL</h2>
        @if ($payment->status === 'paid')
            <span class="bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-md">Lunas</span>
        @elseif ($payment->status === 'pending_confirmation')
            <span class="bg-amber-50 text-amber-700 text-xs px-2.5 py-1 rounded-md">Menunggu Validasi</span>
        @else
            <span class="bg-red-50 text-red-700 text-xs px-2.5 py-1 rounded-md">Belum Bayar</span>
        @endif
    </div>

    @if ($payment->rejection_reason)
        <div class="bg-red-50 border border-red-100 rounded-lg p-3 mb-4">
            <p class="text-xs text-red-700"><i class="ti ti-alert-circle"></i> Konfirmasi sebelumnya ditolak: {{ $payment->rejection_reason }}</p>
        </div>
    @endif

    <table class="w-full text-sm mb-4">
        <tr class="border-t border-slate-100">
            <td class="py-2 text-slate-500">Rumah</td>
            <td class="py-2 text-right">{{ $payment->house->fullLabel() }} <span class="text-xs text-slate-400">({{ $payment->house->isRukem() ? 'Rukem' : 'Non-Rukem' }})</span></td>
        </tr>
        <tr class="border-t border-slate-100">
            <td class="py-2 text-slate-500">Nama</td>
            <td class="py-2 text-right">{{ $payment->house->owner_name }}</td>
        </tr>
        <tr class="border-t border-slate-100">
            <td class="py-2 text-slate-500">Keterangan</td>
            <td class="py-2 text-right">{{ $payment->displayLabel() }}</td>
        </tr>
        <tr class="border-t border-slate-100">
            <td class="py-2 text-slate-500">Tanggal bayar</td>
            <td class="py-2 text-right">{{ $payment->paid_at?->format('d M Y') ?? '-' }}</td>
        </tr>
    </table>

    <div class="border-t border-slate-100 pt-3 mb-4">
        <p class="text-sm text-slate-500 mb-2">Rincian iuran</p>
        @foreach ($payment->resolvedBreakdown() as $label => $amount)
            <div class="flex justify-between text-sm py-1">
                <span class="text-slate-500">{{ $label }}</span>
                <span class="text-slate-700">Rp {{ number_format($amount, 0, ',', '.') }}</span>
            </div>
        @endforeach
        <div class="flex justify-between text-sm font-semibold pt-2 mt-1 border-t border-slate-200">
            <span>Total</span>
            <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
        </div>
    </div>

    @if ($payment->signature)
        <div class="border-t border-slate-100 pt-4 mb-4">
            <p class="text-sm text-slate-500 mb-2">Bukti serah terima (TTD saat penagihan)</p>
            <div class="border border-slate-200 rounded-lg p-3 bg-slate-50 flex items-center justify-center">
                <img src="{{ $payment->signature }}" alt="TTD bukti serah terima" class="h-24">
            </div>
            <p class="text-xs text-slate-400 mt-1">Ditandatangani {{ $payment->signed_at?->format('d M Y, H:i') }}</p>
        </div>
    @endif

    @if ($payment->proof_image)
        <a href="{{ route('resident.payments.download-proof', $payment) }}" class="block text-center bg-slate-50 text-slate-600 text-sm py-2.5 rounded-lg">
            <i class="ti ti-download"></i> Download bukti pembayaran
        </a>
    @endif
</div>

@if ($payment->isUnpaid())
    <a href="{{ route('resident.payments.confirm-form', $payment) }}" class="block w-full text-center mt-4 bg-brand-600 text-white text-sm py-2.5 rounded-lg">
        Konfirmasi Pembayaran
    </a>
@endif

<button onclick="window.print()" class="w-full mt-4 bg-brand-600 text-white text-sm py-2.5 rounded-lg">
    Cetak kwitansi
</button>
@endsection
