<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        p.sub { color: #64748b; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; }
        th { background: #f8fafc; }
    </style>
</head>
<body>
    <h1>Laporan Pembayaran IPL - BerlianPay</h1>
    <p class="sub">Periode: {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</p>

    <table>
        <thead>
            <tr>
                <th>Blok</th>
                <th>No. Rumah</th>
                <th>Nama warga</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Tanggal bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->house->block }}</td>
                    <td>{{ $payment->house->house_number }}</td>
                    <td>{{ $payment->house->owner_name }}</td>
                    <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td>{{ $payment->status === 'paid' ? 'Lunas' : 'Belum bayar' }}</td>
                    <td>{{ $payment->paid_at?->format('d-m-Y') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
