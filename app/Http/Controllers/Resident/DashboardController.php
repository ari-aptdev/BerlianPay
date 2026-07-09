<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua rumah milik user yang login (bisa lebih dari 1)
        $houseIds = $request->user()->houses()->pluck('houses.id');

        $currentPeriodPayments = Payment::with('house')
            ->whereIn('house_id', $houseIds)
            ->where('period_month', now()->month)
            ->where('period_year', now()->year)
            ->get();

        $totalTunggakan = Payment::whereIn('house_id', $houseIds)
            ->where('status', 'unpaid')
            ->where(function ($q) {
                $q->where('period_year', '<', now()->year)
                    ->orWhere(function ($q2) {
                        $q2->where('period_year', now()->year)->where('period_month', '<=', now()->month);
                    });
            })
            ->sum('amount');

        return view('resident.dashboard.index', compact('currentPeriodPayments', 'totalTunggakan'));
    }
}
