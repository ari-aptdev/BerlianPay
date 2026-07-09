@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-medium text-slate-900">Ringkasan</h2>
        <p class="text-sm text-slate-500">{{ now()->translatedFormat('F Y') }}</p>
    </div>
    <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">
        <i class="ti ti-plus"></i> Catat pembayaran
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-sm text-slate-500 mb-1">Pemasukan bulan ini</p>
        <p class="text-2xl font-semibold text-slate-900">Rp {{ number_format($totalIncomeThisMonth, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-sm text-slate-500 mb-1">Warga lunas</p>
        <p class="text-2xl font-semibold text-slate-900">{{ $paidCount }} <span class="text-sm font-normal text-slate-400">/ {{ $activeHouses }}</span></p>
    </div>
    <div class="bg-red-50 rounded-xl border border-red-100 p-4">
        <p class="text-sm text-red-600 mb-1">Warga nunggak</p>
        <p class="text-2xl font-semibold text-red-700">{{ $unpaidCount }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
    <p class="text-sm font-medium text-slate-700 mb-3">Tren pemasukan 6 bulan terakhir</p>
    <canvas id="trendChart" height="80"></canvas>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <p class="text-sm font-medium text-slate-700 px-4 py-3 border-b border-slate-100">Pembayaran terbaru</p>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Rumah</th>
                <th class="text-left px-4 py-2 font-normal">Periode</th>
                <th class="text-left px-4 py-2 font-normal">Nominal</th>
                <th class="text-left px-4 py-2 font-normal">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recentPayments as $payment)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">{{ $payment->house->fullLabel() }}</td>
                    <td class="px-4 py-2.5">{{ $payment->periodLabel() }}</td>
                    <td class="px-4 py-2.5">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5">
                        @if ($payment->status === 'paid')
                            <span class="bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-md">Lunas</span>
                        @else
                            <span class="bg-red-50 text-red-700 text-xs px-2.5 py-1 rounded-md">Belum bayar</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Belum ada data pembayaran.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: @json($trend->pluck('label')),
            datasets: [{
                label: 'Pemasukan',
                data: @json($trend->pluck('total')),
                borderColor: '#1a45c0',
                backgroundColor: 'rgba(26,69,192,0.08)',
                fill: true,
                tension: 0.3,
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { ticks: { callback: (v) => 'Rp ' + (v/1000) + 'rb' } } }
        }
    });
</script>
@endsection
