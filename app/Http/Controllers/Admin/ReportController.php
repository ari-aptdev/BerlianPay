<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\CashLedger;
use App\Support\SimpleXlsxWriter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:reports,view']);
    }

    public static function bulanLabel(int $month): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $bulan[$month] ?? (string) $month;
    }

    public function index(Request $request)
    {
        $month = (int) ($request->month ?? now()->month);
        $year = (int) ($request->year ?? now()->year);

        $ledger = CashLedger::build($month, $year);

        return view('admin.reports.index', array_merge($ledger, compact('month', 'year')));
    }

    public function exportPdf(Request $request)
    {
        $month = (int) ($request->month ?? now()->month);
        $year = (int) ($request->year ?? now()->year);

        $ledger = CashLedger::build($month, $year);

        $perumahanNama = Setting::get('perumahan_nama', 'BerlianPay');
        $logoPath = Setting::get('perumahan_logo_path');
        $logoAbsolutePath = $logoPath ? storage_path('app/public/'.$logoPath) : null;

        $pdf = Pdf::loadView('admin.reports.pdf', array_merge($ledger, [
            'month' => $month,
            'year' => $year,
            'bulanLabel' => self::bulanLabel($month),
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

        $ledger = CashLedger::build($month, $year);
        $perumahanNama = Setting::get('perumahan_nama', 'BerlianPay');

        $rows = [];
        $rows[] = [$perumahanNama.' - Laporan Kas IPL'];
        $rows[] = ['Periode: '.self::bulanLabel($month).' '.$year];
        $rows[] = [];
        $rows[] = ['Saldo Awal', $ledger['startingBalance']];
        $rows[] = [];
        $rows[] = ['Tanggal', 'Keterangan', 'Masuk', 'Keluar', 'Saldo Akhir'];

        foreach ($ledger['entries'] as $entry) {
            $rows[] = [
                $entry['date']->format('d-m-Y'),
                $entry['description'],
                $entry['masuk'] ?: '',
                $entry['keluar'] ?: '',
                $entry['saldo_akhir'],
            ];
        }

        $rows[] = [];
        $rows[] = ['Total Masuk', '', $ledger['totalMasuk']];
        $rows[] = ['Total Keluar', '', '', $ledger['totalKeluar']];
        $rows[] = ['Saldo Akhir Bulan Ini', '', '', '', $ledger['endingBalance']];

        $content = SimpleXlsxWriter::generate($rows);
        $filename = "laporan-kas-ipl-{$year}-{$month}.xlsx";

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
