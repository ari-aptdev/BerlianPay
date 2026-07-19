<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1e293b; }
        .header { display: table; width: 100%; margin-bottom: 12px; }
        .header-logo { display: table-cell; width: 60px; vertical-align: middle; }
        .header-logo img { width: 50px; height: 50px; object-fit: contain; }
        .header-text { display: table-cell; vertical-align: middle; padding-left: 10px; }
        h1 { font-size: 16px; margin: 0 0 2px; }
        p.sub { color: #64748b; margin: 0; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.data th, table.data td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        table.data th { background: #f8fafc; }
        table.data td.right, table.data th.right { text-align: right; }
        .saldo-row td { background: #f8fafc; font-style: italic; color: #64748b; }
        table.summary { width: 280px; margin-left: auto; border-collapse: collapse; margin-top: 12px; }
        table.summary td { padding: 5px 8px; }
        table.summary td:last-child { text-align: right; font-weight: bold; }
        .total-row td { border-top: 2px solid #1e293b; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        @if ($logoAbsolutePath)
            <div class="header-logo"><img src="{{ $logoAbsolutePath }}" alt="Logo"></div>
        @endif
        <div class="header-text">
            <h1>{{ $perumahanNama }} - Laporan Kas IPL</h1>
            <p class="sub">Periode: {{ $bulanLabel }} {{ $year }}</p>
        </div>
    </div>

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
                <td class="right">Rp {{ number_format($startingBalance, 0, ',', '.') }}</td>
            </tr>
            @forelse ($entries as $entry)
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
        <tr><td>Saldo Awal</td><td>Rp {{ number_format($startingBalance, 0, ',', '.') }}</td></tr>
        <tr><td>Total Masuk</td><td>Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td></tr>
        <tr><td>Total Keluar</td><td>Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td></tr>
        <tr class="total-row"><td>Saldo Akhir</td><td>Rp {{ number_format($endingBalance, 0, ',', '.') }}</td></tr>
    </table>
</body>
</html>
