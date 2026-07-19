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

    protected function buildBoth(int $month, int $year): array
    {
        return [
            'general' => CashLedger::build($month, $year, 'general'),
            'security' => CashLedger::build($month, $year, 'security'),
        ];
    }

    public function index(Request $request)
    {
        $month = (int) ($request->month ?? now()->month);
        $year = (int) ($request->year ?? now()->year);

        $ledgers = $this->buildBoth($month, $year);

        return view('admin.reports.index', array_merge($ledgers, compact('month', 'year')));
    }

    /**
     * Logo cuma disisipkan kalau extension GD aktif (dibutuhkan dompdf buat proses gambar)
     * DAN file-nya beneran ada. Kalau salah satu gak terpenuhi, logo di-skip diam-diam
     * daripada bikin export PDF gagal total.
     */
    public static function resolveLogoPath(): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $logoPath = Setting::get('perumahan_logo_path');
        if (! $logoPath) {
            return null;
        }

        $absolutePath = storage_path('app/public/'.$logoPath);

        return file_exists($absolutePath) ? $absolutePath : null;
    }

    public function exportPdf(Request $request)
    {
        $month = (int) ($request->month ?? now()->month);
        $year = (int) ($request->year ?? now()->year);

        $ledgers = $this->buildBoth($month, $year);

        $pdf = Pdf::loadView('admin.reports.pdf', array_merge($ledgers, [
            'month' => $month,
            'year' => $year,
            'bulanLabel' => self::bulanLabel($month),
            'perumahanNama' => Setting::get('perumahan_nama', 'BerlianPay'),
            'logoAbsolutePath' => self::resolveLogoPath(),
        ]));

        return $pdf->download("laporan-kas-{$year}-{$month}.pdf");
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

        $ledgers = $this->buildBoth($month, $year);
        $perumahanNama = Setting::get('perumahan_nama', 'BerlianPay');

        $rows = [];
        $rows[] = [$perumahanNama.' - Laporan Keuangan Kas RT 003/RW 023'];
        $rows[] = ['Periode: '.self::bulanLabel($month).' '.$year];

        foreach (['general' => 'LAPORAN KEUANGAN IPL', 'security' => 'LAPORAN KEUANGAN KAS SECURITY'] as $key => $sectionTitle) {
            $ledger = $ledgers[$key];
            $rows[] = [];
            $rows[] = [$sectionTitle];
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
            $rows[] = ['Saldo Akhir', '', '', '', $ledger['endingBalance']];
        }

        $content = SimpleXlsxWriter::generate($rows);
        $filename = "laporan-kas-{$year}-{$month}.xlsx";

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
