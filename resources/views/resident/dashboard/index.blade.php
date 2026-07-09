@extends('layouts.resident')

@section('content')
<p class="text-sm text-slate-500 mb-1">Halo,</p>
<h2 class="text-lg font-medium text-slate-900 mb-6">{{ auth()->user()->name }}</h2>

@forelse ($currentPeriodPayments as $payment)
    <div class="rounded-xl p-4 mb-3 {{ $payment->status === 'paid' ? 'bg-green-50 border border-green-100' : 'bg-red-50 border border-red-100' }}">
        <div class="flex items-center gap-2 mb-1">
            <i class="ti {{ $payment->status === 'paid' ? 'ti-circle-check text-green-600' : 'ti-alert-circle text-red-600' }} text-base"></i>
            <span class="text-sm font-medium {{ $payment->status === 'paid' ? 'text-green-700' : 'text-red-700' }}">
                {{ $payment->house->fullLabel() }} &middot; {{ $payment->status === 'paid' ? 'Lunas' : 'Belum lunas' }}
            </span>
        </div>
        <p class="text-xl font-semibold {{ $payment->status === 'paid' ? 'text-green-700' : 'text-red-700' }}">
            Rp {{ number_format($payment->amount, 0, ',', '.') }}
        </p>
        <p class="text-xs {{ $payment->status === 'paid' ? 'text-green-600' : 'text-red-600' }} mt-1">IPL {{ $payment->periodLabel() }}</p>
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

<a href="{{ route('resident.payments.index') }}" class="block text-center mt-6 text-sm text-brand-600 font-medium">
    Lihat semua riwayat pembayaran &rarr;
</a>
@endsection
