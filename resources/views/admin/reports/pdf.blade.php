<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1e293b; }
        .header { display: table; width: 100%; margin-bottom: 16px; }
        .header-text { display: table-cell; vertical-align: middle; width: 80%; }
        .header-logo { display: table-cell; width: 20%; vertical-align: middle; text-align: right; }
        .header-logo img { width: 42px; height: 42px; object-fit: contain; }
        h1 { font-size: 16px; margin: 0 0 2px; }
        p.sub { color: #64748b; margin: 0; }
        h2 { font-size: 14px; margin: 22px 0 8px; padding-top: 10px; border-top: 1px solid #e2e8f0; }
        h2.first { border-top: none; margin-top: 0; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.data th, table.data td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        table.data th { background: #f8fafc; }
        table.data td.right, table.data th.right { text-align: right; }
        .saldo-row td { background: #f8fafc; font-style: italic; color: #64748b; }
        table.summary { width: 280px; margin-left: auto; border-collapse: collapse; margin-top: 8px; }
        table.summary td { padding: 5px 8px; }
        table.summary td:last-child { text-align: right; font-weight: bold; }
        .total-row td { border-top: 2px solid #1e293b; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-text">
            <h1>{{ $perumahanNama }} - Laporan Keuangan Kas RT 003/RW 023</h1>
            <p class="sub">Periode: {{ $bulanLabel }} {{ $year }}</p>
        </div>
        @if ($logoAbsolutePath)
            <div class="header-logo"><img src="{{ $logoAbsolutePath }}" alt="Logo"></div>
        @endif
    </div>

    @foreach (['general' => ['title' => 'Laporan Keuangan IPL', 'ledger' => $general], 'security' => ['title' => 'Laporan Keuangan Kas Security', 'ledger' => $security]] as $key => $section)
        @php $ledger = $section['ledger']; @endphp
        <h2 class="{{ $loop->first ? 'first' : '' }}">{{ $section['title'] }}</h2>
        <table class="data">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th class="right">Masuk</th>
                    <th class="right">Keluar</th>
                    <th class="right">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                <tr class="saldo-row">
                    <td colspan="4">Saldo awal bulan ini</td>
                    <td class="right">Rp {{ number_format($ledger['startingBalance'], 0, ',', '.') }}</td>
                </tr>
                @forelse ($ledger['entries'] as $entry)
                    <tr>
                        <td>{{ $entry['date']->format('d-m-Y') }}</td>
                        <td>{{ $entry['description'] }}</td>
                        <td class="right">{{ $entry['masuk'] ? 'Rp '.number_format($entry['masuk'], 0, ',', '.') : '-' }}</td>
                        <td class="right">{{ $entry['keluar'] ? 'Rp '.number_format($entry['keluar'], 0, ',', '.') : '-' }}</td>
                        <td class="right">Rp {{ number_format($entry['saldo_akhir'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Tidak ada transaksi bulan ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <table class="summary">
            <tr><td>Saldo Awal</td><td>Rp {{ number_format($ledger['startingBalance'], 0, ',', '.') }}</td></tr>
            <tr><td>Total Masuk</td><td>Rp {{ number_format($ledger['totalMasuk'], 0, ',', '.') }}</td></tr>
            <tr><td>Total Keluar</td><td>Rp {{ number_format($ledger['totalKeluar'], 0, ',', '.') }}</td></tr>
            <tr class="total-row"><td>Saldo Akhir</td><td>Rp {{ number_format($ledger['endingBalance'], 0, ',', '.') }}</td></tr>
        </table>
    @endforeach
</body>
</html>
