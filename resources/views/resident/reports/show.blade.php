@extends('layouts.resident')

@section('content')
<a href="{{ route('resident.reports.index') }}" class="text-sm text-slate-400 mb-4 inline-block">&larr; Kembali</a>

<h2 class="text-lg font-medium text-slate-900 mb-1">Laporan {{ $bulanLabel }} {{ $year }}</h2>
<p class="text-sm text-slate-500 mb-4">Dikelola oleh pengurus perumahan</p>

@php
    $sections = [
        'general' => ['title' => 'Kas IPL (Umum)', 'ledger' => $general],
        'security' => ['title' => 'Kas Security', 'ledger' => $security],
    ];
@endphp

@foreach ($sections as $key => $section)
    @php $ledger = $section['ledger']; @endphp
    <h3 class="text-sm font-semibold text-slate-800 mb-2 mt-6 first:mt-0">{{ $section['title'] }}</h3>

    <div class="grid grid-cols-2 gap-3 mb-3">
        <div class="bg-green-50 border border-green-100 rounded-xl p-3">
            <p class="text-xs text-green-600">Total Masuk</p>
            <p class="text-base font-semibold text-green-700">Rp {{ number_format($ledger['totalMasuk'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-red-50 border border-red-100 rounded-xl p-3">
            <p class="text-xs text-red-600">Total Keluar</p>
            <p class="text-base font-semibold text-red-700">Rp {{ number_format($ledger['totalKeluar'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-4 mb-3">
        <p class="text-sm text-slate-500">Saldo Akhir</p>
        <p class="text-xl font-semibold {{ $ledger['endingBalance'] >= 0 ? 'text-brand-700' : 'text-red-600' }}">Rp {{ number_format($ledger['endingBalance'], 0, ',', '.') }}</p>
    </div>

    <p class="text-xs font-medium text-slate-500 mb-2">Rincian Transaksi</p>
    <div class="space-y-2 mb-2">
        <div class="flex justify-between text-xs text-slate-400 border-b border-slate-100 pb-2">
            <span>Saldo awal bulan ini</span>
            <span class="font-medium">Rp {{ number_format($ledger['startingBalance'], 0, ',', '.') }}</span>
        </div>
        @forelse ($ledger['entries'] as $entry)
            <div class="border-b border-slate-100 pb-2">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-700">{{ $entry['description'] }}</span>
                </div>
                <div class="flex justify-between text-xs text-slate-400 mt-0.5">
                    <span>{{ $entry['date']->format('d M Y') }}</span>
                    <span>
                        @if ($entry['masuk'])
                            <span class="text-green-600">+Rp {{ number_format($entry['masuk'], 0, ',', '.') }}</span>
                        @else
                            <span class="text-red-500">-Rp {{ number_format($entry['keluar'], 0, ',', '.') }}</span>
                        @endif
                        &middot; Saldo Rp {{ number_format($entry['saldo_akhir'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400 text-center py-4">Tidak ada transaksi bulan ini.</p>
        @endforelse
    </div>
@endforeach

<a href="{{ route('resident.reports.pdf', ['month' => $month, 'year' => $year]) }}" class="block w-full text-center bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium py-3 rounded-xl mt-4">
    <i class="ti ti-download"></i> Download Laporan (PDF)
</a>
@endsection
