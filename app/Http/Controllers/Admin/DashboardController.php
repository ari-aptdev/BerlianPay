<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\Payment;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $month = now()->month;
        $year = now()->year;

        $totalIncomeThisMonth = Payment::where('period_month', $month)
            ->where('period_year', $year)
            ->where('status', 'paid')
            ->sum('amount');

        $activeHouses = House::where('is_active', true)->count();

        $paidCount = Payment::where('period_month', $month)
            ->where('period_year', $year)
            ->where('status', 'paid')
            ->count();

        $unpaidCount = max($activeHouses - $paidCount, 0);

        $recentPayments = Payment::with('house')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Tren 6 bulan terakhir untuk chart
        $trend = collect(range(5, 0))->map(function ($i) {
            $date = Carbon::now()->subMonths($i);
            $total = Payment::where('period_month', $date->month)
                ->where('period_year', $date->year)
                ->where('status', 'paid')
                ->sum('amount');

            return [
                'label' => $date->translatedFormat('M Y'),
                'total' => (int) $total,
            ];
        });

        return view('admin.dashboard.index', compact(
            'totalIncomeThisMonth',
            'activeHouses',
            'paidCount',
            'unpaidCount',
            'recentPayments',
            'trend',
        ));
    }
}
