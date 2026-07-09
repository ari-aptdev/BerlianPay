<x-mail::message>
# Pengingat Pembayaran IPL

Halo **{{ $house->owner_name }}** ({{ $house->fullLabel() }}),

@if($reminderType === 'h_minus_3')
Ini pengingat bahwa pembayaran IPL periode **{{ $periodLabel }}** akan segera jatuh tempo dalam 3 hari.
@elseif($reminderType === 'h_day')
Pembayaran IPL periode **{{ $periodLabel }}** jatuh tempo **hari ini** dan belum terkonfirmasi.
@else
Kami catat masih ada tunggakan IPL yang belum diselesaikan.
@endif

@if($totalTunggakan > 0)
Total tunggakan saat ini: **Rp {{ number_format($totalTunggakan, 0, ',', '.') }}**
@endif

Silakan lakukan pembayaran melalui transfer manual ke rekening pengelola, lalu konfirmasi ke admin agar segera dicatat di sistem.

<x-mail::button :url="config('app.url')">
Buka BerlianPay
</x-mail::button>

Terima kasih,<br>
Pengelola Perumahan
</x-mail::message>
