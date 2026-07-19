@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-6">Laporan Kas</h2>

<form method="GET" class="flex flex-wrap items-end gap-3 mb-6">
    <div>
        <label class="block text-xs text-slate-500 mb-1">Bulan</label>
        <select name="month" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" @selected($month == $m)>{{ \App\Http\Controllers\Admin\ReportController::bulanLabel($m) }}</option>
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

@php
    $sections = [
        'general' => ['title' => 'Kas IPL (Umum)', 'ledger' => $general, 'accent' => 'brand'],
        'security' => ['title' => 'Kas Security', 'ledger' => $security, 'accent' => 'slate'],
    ];
@endphp

@foreach ($sections as $key => $section)
    @php $ledger = $section['ledger']; @endphp
    <h3 class="text-base font-semibold text-slate-800 mb-3 mt-8 first:mt-0">{{ $section['title'] }}</h3>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Saldo Awal</p>
            <p class="text-lg font-semibold text-slate-700">Rp {{ number_format($ledger['startingBalance'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Total Masuk</p>
            <p class="text-lg font-semibold text-green-700">Rp {{ number_format($ledger['totalMasuk'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Total Keluar</p>
            <p class="text-lg font-semibold text-red-600">Rp {{ number_format($ledger['totalKeluar'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <p class="text-sm text-slate-500">Saldo Akhir</p>
            <p class="text-lg font-semibold {{ $ledger['endingBalance'] >= 0 ? 'text-brand-700' : 'text-red-600' }}">Rp {{ number_format($ledger['endingBalance'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-x-auto mb-8">
        <table class="w-full text-sm min-w-[640px]">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="text-left px-4 py-2 font-normal">Tanggal</th>
                    <th class="text-left px-4 py-2 font-normal">Keterangan</th>
                    <th class="text-right px-4 py-2 font-normal">Masuk</th>
                    <th class="text-right px-4 py-2 font-normal">Keluar</th>
                    <th class="text-right px-4 py-2 font-normal">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-t border-slate-100 bg-slate-50/50">
                    <td class="px-4 py-2.5 text-slate-400" colspan="4">Saldo awal bulan ini</td>
                    <td class="px-4 py-2.5 text-right font-medium">Rp {{ number_format($ledger['startingBalance'], 0, ',', '.') }}</td>
                </tr>
                @forelse ($ledger['entries'] as $entry)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-2.5">{{ $entry['date']->format('d-m-Y') }}</td>
                        <td class="px-4 py-2.5">{{ $entry['description'] }}</td>
                        <td class="px-4 py-2.5 text-right text-green-700">{{ $entry['masuk'] ? 'Rp '.number_format($entry['masuk'], 0, ',', '.') : '-' }}</td>
                        <td class="px-4 py-2.5 text-right text-red-600">{{ $entry['keluar'] ? 'Rp '.number_format($entry['keluar'], 0, ',', '.') : '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-medium">Rp {{ number_format($entry['saldo_akhir'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Tidak ada transaksi bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endforeach

<div class="flex items-center justify-between mb-3">
    <h3 class="text-sm font-medium text-slate-700">Kelola Pengeluaran Bulan Ini</h3>
</div>

@if (auth()->user()->canAccess('reports', 'edit'))
<div class="bg-white rounded-xl border border-slate-200 overflow-x-auto mb-4">
    <table class="w-full text-sm min-w-[720px]">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Tanggal</th>
                <th class="text-left px-4 py-2 font-normal">Kategori</th>
                <th class="text-left px-4 py-2 font-normal">Jenis</th>
                <th class="text-left px-4 py-2 font-normal">Keterangan</th>
                <th class="text-left px-4 py-2 font-normal">Nominal</th>
                <th class="text-right px-4 py-2 font-normal">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $allExpenseEntries = $general['entries']->filter(fn ($e) => isset($e['model']))
                    ->concat($security['entries']->filter(fn ($e) => isset($e['model'])))
                    ->sortBy(fn ($e) => $e['model']->expense_date);
            @endphp
            @forelse ($allExpenseEntries as $entry)
                @php $expense = $entry['model']; @endphp
                <form id="expense-form-{{ $expense->id }}" method="POST" action="{{ route('admin.expenses.update', $expense) }}">
                    @csrf @method('PUT')
                </form>
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">
                        <input type="date" form="expense-form-{{ $expense->id }}" name="expense_date" value="{{ $expense->expense_date->format('Y-m-d') }}" class="bg-transparent border-0 p-0 text-sm focus:ring-1 focus:ring-brand-600 rounded">
                    </td>
                    <td class="px-4 py-2.5">
                        <select form="expense-form-{{ $expense->id }}" name="category" class="bg-transparent border-0 p-0 text-sm focus:ring-1 focus:ring-brand-600 rounded">
                            <option value="general" @selected($expense->category === 'general')>Kas IPL (Umum)</option>
                            <option value="security" @selected($expense->category === 'security')>Security</option>
                        </select>
                    </td>
                    <td class="px-4 py-2.5">
                        @if ($expense->category === 'security')
                            <select form="expense-form-{{ $expense->id }}" name="type" class="bg-transparent border-0 p-0 text-sm focus:ring-1 focus:ring-brand-600 rounded">
                                <option value="income" @selected($expense->type === 'income')>Masuk</option>
                                <option value="expense" @selected($expense->type === 'expense')>Keluar</option>
                            </select>
                        @else
                            <span class="text-slate-400">Keluar</span>
                            <input type="hidden" form="expense-form-{{ $expense->id }}" name="type" value="expense">
                        @endif
                    </td>
                    <td class="px-4 py-2.5">
                        <input type="text" form="expense-form-{{ $expense->id }}" name="description" value="{{ $expense->description }}" class="w-full bg-transparent border-0 p-0 text-sm focus:ring-1 focus:ring-brand-600 rounded">
                    </td>
                    <td class="px-4 py-2.5">
                        <input type="number" form="expense-form-{{ $expense->id }}" name="amount" value="{{ $expense->amount }}" class="w-28 bg-transparent border-0 p-0 text-sm focus:ring-1 focus:ring-brand-600 rounded">
                    </td>
                    <td class="px-4 py-2.5 text-right space-x-2">
                        <button type="submit" form="expense-form-{{ $expense->id }}" class="text-brand-600 hover:underline">Simpan</button>
                        <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}" class="inline" onsubmit="return confirm('Hapus transaksi ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada transaksi manual dicatat bulan ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-5 max-w-lg">
    <p class="text-sm font-medium text-slate-700 mb-3">Catat Transaksi Baru</p>
    <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-3">
        @csrf
        <input type="hidden" name="period_month" value="{{ $month }}">
        <input type="hidden" name="period_year" value="{{ $year }}">
        <div>
            <label class="block text-sm text-slate-600 mb-1.5">Kategori Kas</label>
            <select name="category" id="expenseCategorySelect" onchange="document.getElementById('expenseTypeWrap').classList.toggle('hidden', this.value !== 'security')" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
                <option value="general">Kas IPL (Umum)</option>
                <option value="security">Security</option>
            </select>
            @error('category') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-slate-400 mt-1">Kas IPL (Umum): pemasukan otomatis dari pembayaran warga, di sini cuma buat catat pengeluaran. Security: pemasukan & pengeluaran dicatat manual semua.</p>
        </div>
        <div id="expenseTypeWrap" class="hidden">
            <label class="block text-sm text-slate-600 mb-1.5">Jenis Transaksi</label>
            <select name="type" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
                <option value="income">Masuk (Pemasukan)</option>
                <option value="expense">Keluar (Pengeluaran)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1.5">Tanggal</label>
            <input type="date" name="expense_date" value="{{ now()->toDateString() }}" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('expense_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1.5">Keterangan (dipakai untuk apa)</label>
            <input type="text" name="description" placeholder="Mis. Gaji satpam bulan ini" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
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
@endif
@endsection
