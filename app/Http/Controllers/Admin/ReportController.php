<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PaymentsExport;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

    public function exportExcel(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        return Excel::download(new PaymentsExport($month, $year), "laporan-ipl-{$year}-{$month}.xlsx");
    }
}
