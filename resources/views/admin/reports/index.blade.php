@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-6">Laporan Kas IPL</h2>

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
        <i class="ti ti-file-spreadsheet"></i> Export Excel (.xlsx)
    </a>
</form>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-sm text-slate-500">Pemasukan (Lunas)</p>
        <p class="text-xl font-semibold text-green-700">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-sm text-slate-500">Pengeluaran</p>
        <p class="text-xl font-semibold text-red-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <p class="text-sm text-slate-500">Saldo Kas</p>
        <p class="text-xl font-semibold {{ $saldo >= 0 ? 'text-brand-700' : 'text-red-600' }}">Rp {{ number_format($saldo, 0, ',', '.') }}</p>
    </div>
</div>

<h3 class="text-sm font-medium text-slate-700 mb-3">Pemasukan — Pembayaran IPL Warga</h3>
<div class="bg-white rounded-xl border border-slate-200 overflow-x-auto mb-8">
    <table class="w-full text-sm min-w-[680px]">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Rumah</th>
                <th class="text-left px-4 py-2 font-normal">Nama</th>
                <th class="text-left px-4 py-2 font-normal">Status IPL</th>
                <th class="text-left px-4 py-2 font-normal">Keterangan</th>
                <th class="text-left px-4 py-2 font-normal">Nominal</th>
                <th class="text-left px-4 py-2 font-normal">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">{{ $payment->house->fullLabel() }}</td>
                    <td class="px-4 py-2.5">{{ $payment->house->owner_name }}</td>
                    <td class="px-4 py-2.5">{{ $payment->house->isRukem() ? 'Rukem' : 'Non-Rukem' }}</td>
                    <td class="px-4 py-2.5">{{ $payment->displayLabel() }}</td>
                    <td class="px-4 py-2.5">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5">{{ $payment->statusLabel() }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">Tidak ada data untuk periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="flex items-center justify-between mb-3">
    <h3 class="text-sm font-medium text-slate-700">Pengeluaran Bulan Ini</h3>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-x-auto mb-4">
    <table class="w-full text-sm min-w-[500px]">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Keterangan</th>
                <th class="text-left px-4 py-2 font-normal">Nominal</th>
                <th class="text-left px-4 py-2 font-normal">Dicatat oleh</th>
                <th class="text-right px-4 py-2 font-normal">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenses as $expense)
                <form id="expense-form-{{ $expense->id }}" method="POST" action="{{ route('admin.expenses.update', $expense) }}">
                    @csrf @method('PUT')
                </form>
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">
                        <input type="text" form="expense-form-{{ $expense->id }}" name="description" value="{{ $expense->description }}" class="w-full bg-transparent border-0 p-0 text-sm focus:ring-1 focus:ring-brand-600 rounded">
                    </td>
                    <td class="px-4 py-2.5">
                        <input type="number" form="expense-form-{{ $expense->id }}" name="amount" value="{{ $expense->amount }}" class="w-28 bg-transparent border-0 p-0 text-sm focus:ring-1 focus:ring-brand-600 rounded">
                    </td>
                    <td class="px-4 py-2.5 text-slate-500">{{ $expense->recordedBy?->name ?? '-' }}</td>
                    <td class="px-4 py-2.5 text-right space-x-2">
                        <button type="submit" form="expense-form-{{ $expense->id }}" class="text-brand-600 hover:underline">Simpan</button>
                        <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" class="inline" onsubmit="return confirm('Hapus pengeluaran ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Belum ada pengeluaran dicatat bulan ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-5 max-w-lg">
    <p class="text-sm font-medium text-slate-700 mb-3">Catat Pengeluaran Baru</p>
    <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-3">
        @csrf
        <input type="hidden" name="period_month" value="{{ $month }}">
        <input type="hidden" name="period_year" value="{{ $year }}">
        <div>
            <label class="block text-sm text-slate-600 mb-1.5">Keterangan</label>
            <input type="text" name="description" placeholder="Mis. Perbaikan lampu jalan Blok C" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1.5">Nominal (Rp)</label>
            <input type="number" name="amount" min="0" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('amount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg w-full sm:w-auto">Simpan Pengeluaran</button>
    </form>
</div>
@endsection
