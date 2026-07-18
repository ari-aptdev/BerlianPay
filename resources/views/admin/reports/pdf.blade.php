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
        h2 { font-size: 13px; margin: 18px 0 6px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.data th, table.data td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        table.data th { background: #f8fafc; }
        table.summary { width: 260px; margin-left: auto; border-collapse: collapse; margin-top: 10px; }
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

    <h2>Pemasukan — Pembayaran IPL Warga</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Blok</th>
                <th>No. Rumah</th>
                <th>Nama warga</th>
                <th>Status IPL</th>
                <th>Keterangan</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Tanggal bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td>{{ $payment->house->block }}</td>
                    <td>{{ $payment->house->house_number }}</td>
                    <td>{{ $payment->house->owner_name }}</td>
                    <td>{{ $payment->house->isRukem() ? 'Rukem' : 'Non-Rukem' }}</td>
                    <td>{{ $payment->displayLabel() }}</td>
                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td>{{ $payment->statusLabel() }}</td>
                    <td>{{ $payment->paid_at?->format('d-m-Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Tidak ada data untuk periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Pengeluaran</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th>Nominal</th>
                <th>Dicatat oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expenses as $expense)
                <tr>
                    <td>{{ $expense->description }}</td>
                    <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                    <td>{{ $expense->recordedBy?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Tidak ada pengeluaran dicatat bulan ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr><td>Total Pemasukan</td><td>Rp {{ number_format($totalPaid, 0, ',', '.') }}</td></tr>
        <tr><td>Total Pengeluaran</td><td>Rp {{ number_format($totalExpense, 0, ',', '.') }}</td></tr>
        <tr class="total-row"><td>Saldo Kas</td><td>Rp {{ number_format($saldo, 0, ',', '.') }}</td></tr>
    </table>
</body>
</html>
