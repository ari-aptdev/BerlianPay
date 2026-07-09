@extends('layouts.resident')

@section('content')
<a href="{{ route('resident.payments.index') }}" class="text-sm text-slate-400 mb-4 inline-block">&larr; Kembali</a>

<div class="bg-white border border-slate-200 rounded-xl p-5">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-medium text-slate-900">Kwitansi IPL</h2>
        @if ($payment->status === 'paid')
            <span class="bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-md">Lunas</span>
        @else
            <span class="bg-red-50 text-red-700 text-xs px-2.5 py-1 rounded-md">Belum bayar</span>
        @endif
    </div>

    <table class="w-full text-sm mb-4">
        <tr class="border-t border-slate-100">
            <td class="py-2 text-slate-500">Rumah</td>
            <td class="py-2 text-right">{{ $payment->house->fullLabel() }}</td>
        </tr>
        <tr class="border-t border-slate-100">
            <td class="py-2 text-slate-500">Nama</td>
            <td class="py-2 text-right">{{ $payment->house->owner_name }}</td>
        </tr>
        <tr class="border-t border-slate-100">
            <td class="py-2 text-slate-500">Periode</td>
            <td class="py-2 text-right">{{ $payment->periodLabel() }}</td>
        </tr>
        <tr class="border-t border-slate-100">
            <td class="py-2 text-slate-500">Nominal</td>
            <td class="py-2 text-right font-medium">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
        </tr>
        <tr class="border-t border-slate-100">
            <td class="py-2 text-slate-500">Tanggal bayar</td>
            <td class="py-2 text-right">{{ $payment->paid_at?->format('d M Y') ?? '-' }}</td>
        </tr>
    </table>

    @if ($payment->proof_image)
        <a href="{{ route('resident.payments.download-proof', $payment) }}" class="block text-center bg-slate-50 text-slate-600 text-sm py-2.5 rounded-lg">
            <i class="ti ti-download"></i> Download bukti pembayaran
        </a>
    @endif
</div>

<button onclick="window.print()" class="w-full mt-4 bg-brand-600 text-white text-sm py-2.5 rounded-lg">
    Cetak kwitansi
</button>
@endsection
