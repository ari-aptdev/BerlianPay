<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Setting;
use App\Support\SimpleXlsxWriter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:reports,view']);
    }

    protected function bulanLabel(int $month): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $bulan[$month] ?? (string) $month;
    }

    protected function loadData(int $month, int $year): array
    {
        $payments = Payment::with('house')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('house_id')
            ->get();

        $expenses = Expense::where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('created_at')
            ->get();

        $totalPaid = $payments->where('status', 'paid')->sum('amount');
        $totalExpense = $expenses->sum('amount');
        $saldo = $totalPaid - $totalExpense;

        return compact('payments', 'expenses', 'totalPaid', 'totalExpense', 'saldo');
    }

    public function index(Request $request)
    {
        $month = (int) ($request->month ?? now()->month);
        $year = (int) ($request->year ?? now()->year);

        $data = $this->loadData($month, $year);

        return view('admin.reports.index', array_merge($data, compact('month', 'year')));
    }

    public function exportPdf(Request $request)
    {
        $month = (int) ($request->month ?? now()->month);
        $year = (int) ($request->year ?? now()->year);

        $data = $this->loadData($month, $year);

        $perumahanNama = Setting::get('perumahan_nama', 'BerlianPay');
        $logoPath = Setting::get('perumahan_logo_path');
        $logoAbsolutePath = $logoPath ? storage_path('app/public/'.$logoPath) : null;

        $pdf = Pdf::loadView('admin.reports.pdf', array_merge($data, [
            'month' => $month,
            'year' => $year,
            'bulanLabel' => $this->bulanLabel($month),
            'perumahanNama' => $perumahanNama,
            'logoAbsolutePath' => ($logoAbsolutePath && file_exists($logoAbsolutePath)) ? $logoAbsolutePath : null,
        ]));

        return $pdf->download("laporan-kas-ipl-{$year}-{$month}.pdf");
    }

    /**
     * Export .xlsx asli (bukan CSV) tanpa dependency eksternal — lihat catatan
     * di App\Support\SimpleXlsxWriter. Logo TIDAK bisa disisipkan di file Excel
     * (keterbatasan teknis format tanpa library tambahan), tapi nama perumahan
     * tetap dicantumkan sebagai judul. Logo tetap muncul lengkap di export PDF.
     */
    public function exportExcel(Request $request)
    {
        $month = (int) ($request->month ?? now()->month);
        $year = (int) ($request->year ?? now()->year);

        $data = $this->loadData($month, $year);
        $perumahanNama = Setting::get('perumahan_nama', 'BerlianPay');

        $rows = [];
        $rows[] = [$perumahanNama.' - Laporan Kas IPL'];
        $rows[] = ['Periode: '.$this->bulanLabel($month).' '.$year];
        $rows[] = [];
        $rows[] = ['PEMASUKAN (Pembayaran IPL Warga)'];
        $rows[] = ['Blok', 'No. Rumah', 'Nama Warga', 'Status IPL', 'Keterangan', 'Nominal', 'Status', 'Tanggal Bayar'];

        foreach ($data['payments'] as $payment) {
            $rows[] = [
                $payment->house->block,
                $payment->house->house_number,
                $payment->house->owner_name,
                $payment->house->isRukem() ? 'Rukem' : 'Non-Rukem',
                $payment->displayLabel(),
                $payment->amount,
                $payment->status === 'paid' ? 'Lunas' : ($payment->status === 'pending_confirmation' ? 'Menunggu Validasi' : 'Belum Bayar'),
                $payment->paid_at?->format('d-m-Y') ?? '-',
            ];
        }

        $rows[] = [];
        $rows[] = ['Total Pemasukan (Lunas)', '', '', '', '', $data['totalPaid']];
        $rows[] = [];
        $rows[] = ['PENGELUARAN'];
        $rows[] = ['Keterangan', 'Nominal', 'Dicatat oleh'];

        foreach ($data['expenses'] as $expense) {
            $rows[] = [$expense->description, $expense->amount, $expense->recordedBy?->name ?? '-'];
        }

        $rows[] = [];
        $rows[] = ['Total Pengeluaran', $data['totalExpense']];
        $rows[] = [];
        $rows[] = ['SALDO (Pemasukan - Pengeluaran)', $data['saldo']];

        $content = SimpleXlsxWriter::generate($rows);
        $filename = "laporan-kas-ipl-{$year}-{$month}.xlsx";

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
