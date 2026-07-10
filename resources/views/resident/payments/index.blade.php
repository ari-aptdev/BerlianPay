@extends('layouts.resident')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-4">Riwayat pembayaran</h2>

<div class="space-y-3">
    @forelse ($payments as $payment)
        <a href="{{ route('resident.payments.show', $payment) }}" class="block border border-slate-200 rounded-xl p-4 hover:border-brand-200">
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium text-slate-800">{{ $payment->periodLabel() }}</span>
                @if ($payment->status === 'paid')
                    <span class="bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-md">Lunas</span>
                @else
                    <span class="bg-red-50 text-red-700 text-xs px-2.5 py-1 rounded-md">Belum bayar</span>
                @endif
            </div>
            <p class="text-sm text-slate-500">{{ $payment->house->fullLabel() }} &middot; Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>

            @php
                $lastReminder = $payment->house->reminderLogs()
                    ->where('payment_id', $payment->id)
                    ->where('status', 'sent')
                    ->latest('sent_at')
                    ->first();
            @endphp
            @if ($lastReminder)
                <span class="inline-block mt-2 text-xs text-slate-400">
                    <i class="ti ti-bell-ringing text-xs"></i> Reminder terkirim {{ $lastReminder->sent_at->format('d M Y') }}
                </span>
            @endif

            @if ($payment->signature)
                <span class="inline-block mt-2 text-xs text-green-600">
                    <i class="ti ti-signature text-xs"></i> Sudah ada bukti serah terima
                </span>
            @endif
        </a>
    @empty
        <div class="text-center text-sm text-slate-400 py-10">Belum ada riwayat pembayaran.</div>
    @endforelse
</div>

<div class="mt-4">{{ $payments->links() }}</div>
@endsection
