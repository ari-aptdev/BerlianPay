<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        protected int $month,
        protected int $year,
    ) {}

    public function collection()
    {
        return Payment::with('house')
            ->where('period_month', $this->month)
            ->where('period_year', $this->year)
            ->orderBy('house_id')
            ->get();
    }

    public function headings(): array
    {
        return ['Blok', 'No. Rumah', 'Nama Warga', 'Periode', 'Nominal', 'Status', 'Tanggal Bayar'];
    }

    public function map($payment): array
    {
        return [
            $payment->house->block,
            $payment->house->house_number,
            $payment->house->owner_name,
            $payment->periodLabel(),
            $payment->amount,
            $payment->status === 'paid' ? 'Lunas' : 'Belum bayar',
            $payment->paid_at?->format('d-m-Y') ?? '-',
        ];
    }
}
