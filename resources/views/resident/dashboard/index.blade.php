@extends('layouts.resident')

@section('content')
<p class="text-sm text-slate-500 mb-1">Halo,</p>
<h2 class="text-lg font-medium text-slate-900 mb-6">{{ auth()->user()->name }}</h2>

@php
    $statusStyle = [
        'paid' => ['bg' => 'bg-green-50 border border-green-100', 'text' => 'text-green-700', 'icon' => 'ti-circle-check'],
        'pending_confirmation' => ['bg' => 'bg-amber-50 border border-amber-100', 'text' => 'text-amber-700', 'icon' => 'ti-clock'],
        'unpaid' => ['bg' => 'bg-red-50 border border-red-100', 'text' => 'text-red-700', 'icon' => 'ti-alert-circle'],
    ];
@endphp

@forelse ($currentPeriodPayments as $payment)
    @php $style = $statusStyle[$payment->status]; @endphp
    <div class="rounded-xl p-4 mb-3 {{ $style['bg'] }}">
        <div class="flex items-center gap-2 mb-1">
            <i class="ti {{ $style['icon'] }} {{ $style['text'] }} text-base"></i>
            <span class="text-sm font-medium {{ $style['text'] }}">
                {{ $payment->house->fullLabel() }} &middot; {{ $payment->statusLabel() }}
            </span>
        </div>
        <p class="text-xl font-semibold {{ $style['text'] }}">
            Rp {{ number_format($payment->amount, 0, ',', '.') }}
        </p>
        <p class="text-xs {{ $style['text'] }} mt-1">IPL {{ $payment->periodLabel() }}</p>

        @if ($payment->status === 'unpaid')
            <a href="{{ route('resident.payments.confirm-form', $payment) }}"
               class="block text-center mt-3 bg-white text-red-600 border border-red-200 text-sm font-medium py-2 rounded-lg">
                Konfirmasi Pembayaran
            </a>
        @elseif ($payment->status === 'pending_confirmation')
            <p class="text-xs {{ $style['text'] }} mt-2">Menunggu validasi admin, mohon ditunggu.</p>
        @endif
    </div>
@empty
    <div class="rounded-xl p-4 mb-3 bg-slate-50 border border-slate-200 text-sm text-slate-500">
        Belum ada tagihan untuk bulan ini.
    </div>
@endforelse

@if ($totalTunggakan > 0)
    <div class="rounded-xl p-4 border border-amber-200 bg-amber-50">
        <p class="text-sm text-amber-700">Total tunggakan keseluruhan</p>
        <p class="text-lg font-semibold text-amber-800">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</p>
    </div>
@endif
@endsection
