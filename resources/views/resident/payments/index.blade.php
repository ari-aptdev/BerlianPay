@extends('layouts.resident')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-4">Riwayat Pembayaran {{ $year }}</h2>

<div class="space-y-3">
    @php
        $bulanLabel = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $statusStyle = [
            'paid' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'label' => 'Lunas'],
            'pending_confirmation' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'label' => 'Menunggu Validasi'],
            'unpaid' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'label' => 'Belum Bayar'],
        ];
    @endphp

    @foreach ($months as $row)
        @php $payment = $row['payment']; @endphp
        @if ($payment)
            @php $style = $statusStyle[$payment->status]; @endphp
            <a href="{{ route('resident.payments.show', $payment) }}" class="block border border-slate-200 rounded-xl p-4 hover:border-brand-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-800">{{ $bulanLabel[$row['month']] }} {{ $year }}</span>
                    <span class="{{ $style['bg'] }} {{ $style['text'] }} text-xs px-2.5 py-1 rounded-md">{{ $style['label'] }}</span>
                </div>
                <p class="text-sm text-slate-500 mt-1">{{ $row['house']->fullLabel() }} &middot; <span class="text-[11px] uppercase text-slate-400">{{ $row['house']->isRukem() ? 'Rukem' : 'Non-Rukem' }}</span></p>

                <div class="mt-2 pt-2 border-t border-slate-100 space-y-1">
                    @foreach ($payment->resolvedBreakdown() as $label => $amount)
                        <div class="flex justify-between text-xs text-slate-400">
                            <span>{{ $label }}</span>
                            <span>Rp {{ number_format($amount, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between text-xs font-medium text-slate-600 pt-1">
                        <span>Total</span>
                        <span>Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </a>
        @else
            <div class="border border-slate-100 rounded-xl p-4 opacity-60">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-500">{{ $bulanLabel[$row['month']] }} {{ $year }}</span>
                    <span class="bg-slate-100 text-slate-400 text-xs px-2.5 py-1 rounded-md">Belum Ada Tagihan</span>
                </div>
                <p class="text-sm text-slate-400 mt-1">{{ $row['house']->fullLabel() }}</p>
            </div>
        @endif
    @endforeach
</div>

@if ($otherPayments->isNotEmpty())
    <h3 class="text-sm font-medium text-slate-700 mt-6 mb-3">Tagihan Lain-lain</h3>
    <div class="space-y-3">
        @foreach ($otherPayments as $payment)
            @php $style = $statusStyle[$payment->status]; @endphp
            <a href="{{ route('resident.payments.show', $payment) }}" class="block border border-slate-200 rounded-xl p-4 hover:border-brand-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-800">{{ $payment->displayLabel() }}</span>
                    <span class="{{ $style['bg'] }} {{ $style['text'] }} text-xs px-2.5 py-1 rounded-md">{{ $style['label'] }}</span>
                </div>
                <p class="text-sm text-slate-500 mt-1">{{ $payment->house->fullLabel() }} &middot; Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
            </a>
        @endforeach
    </div>
@endif
@endsection
