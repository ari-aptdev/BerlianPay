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

    protected function buildBoth(int $month, int $year): array
    {
        return [
            'general' => CashLedger::build($month, $year, 'general'),
            'security' => CashLedger::build($month, $year, 'security'),
        ];
    }

    public function show(Request $request, int $month, int $year)
    {
        abort_unless($month >= 1 && $month <= 12, 404);

        $ledgers = $this->buildBoth($month, $year);
        $bulanLabel = AdminReportController::bulanLabel($month);

        return view('resident.reports.show', array_merge($ledgers, compact('month', 'year', 'bulanLabel')));
    }

    public function pdf(Request $request, int $month, int $year)
    {
        abort_unless($month >= 1 && $month <= 12, 404);

        $ledgers = $this->buildBoth($month, $year);

        $pdf = Pdf::loadView('admin.reports.pdf', array_merge($ledgers, [
            'month' => $month,
            'year' => $year,
            'bulanLabel' => AdminReportController::bulanLabel($month),
            'perumahanNama' => Setting::get('perumahan_nama', 'BerlianPay'),
            'logoAbsolutePath' => AdminReportController::resolveLogoPath(),
        ]));

        return $pdf->download("laporan-kas-{$year}-{$month}.pdf");
    }
}
