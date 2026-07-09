@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-6">Laporan pembayaran</h2>

<form method="GET" class="flex flex-wrap items-end gap-3 mb-6">
    <div>
        <label class="block text-xs text-slate-500 mb-1">Bulan</label>
        <select name="month" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" @selected($month == $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
            @endfor
        </select>
    </div>
    <div>
        <label class="block text-xs text-slate-500 mb-1">Tahun</label>
        <input type="number" name="year" value="{{ $year }}" class="w-24 rounded-lg border border-slate-200 px-3 py-2 text-sm">
    </div>
    <button class="bg-slate-100 text-slate-600 text-sm px-4 py-2 rounded-lg">Tampilkan</button>
    <a href="{{ route('admin.reports.export-pdf', ['month' => $month, 'year' => $year]) }}" class="inline-flex items-center gap-2 bg-red-50 text-red-600 text-sm px-4 py-2 rounded-lg">
        <i class="ti ti-file-type-pdf"></i> Export PDF
    </a>
    <a href="{{ route('admin.reports.export-excel', ['month' => $month, 'year' => $year]) }}" class="inline-flex items-center gap-2 bg-green-50 text-green-700 text-sm px-4 py-2 rounded-lg">
        <i class="ti ti-file-type-xls"></i> Export Excel
    </a>
</form>

<div class="bg-white rounded-xl border border-slate-200 p-4 mb-4">
    <p class="text-sm text-slate-500">Total diterima periode ini</p>
    <p class="text-2xl font-semibold text-slate-900">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Rumah</th>
                <th class="text-left px-4 py-2 font-normal">Nama</th>
                <th class="text-left px-4 py-2 font-normal">Nominal</th>
                <th class="text-left px-4 py-2 font-normal">Status</th>
                <th class="text-left px-4 py-2 font-normal">Tanggal bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">{{ $payment->house->fullLabel() }}</td>
                    <td class="px-4 py-2.5">{{ $payment->house->owner_name }}</td>
                    <td class="px-4 py-2.5">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5">{{ $payment->status === 'paid' ? 'Lunas' : 'Belum bayar' }}</td>
                    <td class="px-4 py-2.5">{{ $payment->paid_at?->format('d-m-Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Tidak ada data untuk periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
