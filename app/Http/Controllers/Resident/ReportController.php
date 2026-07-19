<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Models\Setting;
use App\Support\CashLedger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year = now()->year;

        return view('resident.reports.index', compact('year'));
    }

    public function show(Request $request, int $month, int $year)
    {
        abort_unless($month >= 1 && $month <= 12, 404);

        $ledger = CashLedger::build($month, $year);
        $bulanLabel = AdminReportController::bulanLabel($month);

        return view('resident.reports.show', array_merge($ledger, compact('month', 'year', 'bulanLabel')));
    }

    public function pdf(Request $request, int $month, int $year)
    {
        abort_unless($month >= 1 && $month <= 12, 404);

        $ledger = CashLedger::build($month, $year);

        $perumahanNama = Setting::get('perumahan_nama', 'BerlianPay');
        $logoPath = Setting::get('perumahan_logo_path');
        $logoAbsolutePath = $logoPath ? storage_path('app/public/'.$logoPath) : null;

        $pdf = Pdf::loadView('admin.reports.pdf', array_merge($ledger, [
            'month' => $month,
            'year' => $year,
            'bulanLabel' => AdminReportController::bulanLabel($month),
            'perumahanNama' => $perumahanNama,
            'logoAbsolutePath' => ($logoAbsolutePath && file_exists($logoAbsolutePath)) ? $logoAbsolutePath : null,
        ]));

        return $pdf->download("laporan-kas-ipl-{$year}-{$month}.pdf");
    }
}
