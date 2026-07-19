@extends('layouts.resident')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-1">Laporan Keuangan IPL</h2>
<p class="text-sm text-slate-500 mb-4">Laporan kas yang dikelola pengurus, transparan buat semua warga.</p>

<div class="space-y-3">
    @php
        $bulanLabel = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    @endphp
    @for ($m = 1; $m <= 12; $m++)
        <a href="{{ route('resident.reports.show', ['month' => $m, 'year' => $year]) }}" class="flex items-center justify-between border border-slate-200 rounded-xl p-4 hover:border-brand-200">
            <span class="text-sm font-medium text-slate-800">{{ $bulanLabel[$m] }} {{ $year }}</span>
            <i class="ti ti-chevron-right text-slate-400"></i>
        </a>
    @endfor
</div>
@endsection
