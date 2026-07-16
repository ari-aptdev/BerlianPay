@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-6">Konfirmasi Pembayaran Warga</h2>

<div class="space-y-4">
    @forelse ($pending as $payment)
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="text-sm font-medium text-slate-800">{{ $payment->house->fullLabel() }} &middot; {{ $payment->house->owner_name }}</p>
                    <p class="text-sm text-slate-500">IPL {{ $payment->periodLabel() }} &middot; Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">Dikonfirmasi {{ $payment->confirmed_at?->format('d M Y, H:i') }}</p>
                </div>
                <span class="bg-amber-50 text-amber-700 text-xs px-2.5 py-1 rounded-md">Menunggu Validasi</span>
            </div>

            @if ($payment->notes)
                <p class="text-sm text-slate-600 bg-slate-50 rounded-lg p-3 mb-3">"{{ $payment->notes }}"</p>
            @endif

            @if ($payment->proof_image)
                <a href="{{ route('admin.payment-confirmations.view-proof', $payment) }}" target="_blank" class="block mb-3">
                    <img src="{{ route('admin.payment-confirmations.view-proof', $payment) }}" alt="Bukti transfer" class="max-h-64 rounded-lg border border-slate-200">
                </a>
            @endif

            @if (auth()->user()->canAccess('payment_confirmations', 'edit'))
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.payment-confirmations.approve', $payment) }}" onsubmit="return confirm('Setujui pembayaran ini sebagai lunas?')">
                        @csrf @method('PATCH')
                        <button class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-lg">
                            <i class="ti ti-check"></i> Setujui
                        </button>
                    </form>
                    <button type="button" onclick="document.getElementById('reject-form-{{ $payment->id }}').classList.toggle('hidden')"
                        class="bg-red-50 text-red-600 text-sm px-4 py-2 rounded-lg">
                        <i class="ti ti-x"></i> Tolak
                    </button>
                </div>

                <form id="reject-form-{{ $payment->id }}" method="POST" action="{{ route('admin.payment-confirmations.reject', $payment) }}" class="hidden mt-3 flex gap-2">
                    @csrf @method('PATCH')
                    <input type="text" name="rejection_reason" placeholder="Alasan penolakan..." required class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <button class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-lg">Kirim</button>
                </form>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-xl border border-slate-200 p-6 text-center text-slate-400 text-sm">
            Tidak ada konfirmasi pembayaran yang menunggu validasi.
        </div>
    @endforelse
</div>

<div class="mt-4">{{ $pending->links() }}</div>
@endsection
