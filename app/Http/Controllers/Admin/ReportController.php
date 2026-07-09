<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->isAdmin(), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $payments = Payment::with('house')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('house_id')
            ->get();

        $totalPaid = $payments->where('status', 'paid')->sum('amount');

        return view('admin.reports.index', compact('payments', 'month', 'year', 'totalPaid'));
    }

    public function exportPdf(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $payments = Payment::with('house')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('house_id')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact('payments', 'month', 'year'));

        return $pdf->download("laporan-ipl-{$year}-{$month}.pdf");
    }

    /**
     * Export CSV manual (bisa dibuka langsung di Excel/Google Sheets).
     * Sengaja tidak pakai library Excel eksternal (maatwebsite/excel) supaya
     * tidak menambah dependency berat (phpspreadsheet + ext-gd) untuk MVP.
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $payments = Payment::with('house')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('house_id')
            ->get();

        $filename = "laporan-ipl-{$year}-{$month}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function () use ($payments) {
            $out = fopen('php://output', 'w');

            // BOM biar Excel baca UTF-8 dengan benar (karakter Rp, dsb)
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['Blok', 'No. Rumah', 'Nama Warga', 'Periode', 'Nominal', 'Status', 'Tanggal Bayar']);

            foreach ($payments as $payment) {
                fputcsv($out, [
                    $payment->house->block,
                    $payment->house->house_number,
                    $payment->house->owner_name,
                    $payment->periodLabel(),
                    $payment->amount,
                    $payment->status === 'paid' ? 'Lunas' : 'Belum bayar',
                    $payment->paid_at?->format('d-m-Y') ?? '-',
                ]);
            }

            fclose($out);
        }, $filename, $headers);
    }
}

