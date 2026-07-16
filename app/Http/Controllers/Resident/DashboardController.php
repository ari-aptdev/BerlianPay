<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Support\IplPricing;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua rumah milik user yang login (bisa lebih dari 1)
        $houses = $request->user()->houses;

        // Pastikan setiap rumah punya baris tagihan bulan berjalan, dihitung
        // dari tarif Rukem/Non-Rukem yang berlaku SEKARANG dan disnapshot
        // (breakdown + ipl_status) biar histori gak berubah kalau tarif diedit nanti.
        foreach ($houses as $house) {
            $exists = Payment::where('house_id', $house->id)
                ->where('type', 'monthly')
                ->where('period_month', now()->month)
                ->where('period_year', now()->year)
                ->exists();

            if (! $exists) {
                $breakdown = IplPricing::breakdownFor($house->ipl_status);

                Payment::create([
                    'house_id' => $house->id,
                    'type' => 'monthly',
                    'period_month' => now()->month,
                    'period_year' => now()->year,
                    'amount' => array_sum($breakdown),
                    'ipl_status' => $house->ipl_status,
                    'breakdown' => $breakdown,
                    'status' => 'unpaid',
                ]);
            }
        }

        $houseIds = $houses->pluck('id');

        $currentPeriodPayments = Payment::with('house')
            ->whereIn('house_id', $houseIds)
            ->where('type', 'monthly')
            ->where('period_month', now()->month)
            ->where('period_year', now()->year)
            ->get();

        // Tagihan lain-lain yang belum tuntas (misal biaya pendaftaran Rukem),
        // ditampilkan terpisah karena bukan tagihan bulanan biasa.
        $otherPendingPayments = Payment::with('house')
            ->whereIn('house_id', $houseIds)
            ->where('type', '!=', 'monthly')
            ->whereIn('status', ['unpaid', 'pending_confirmation'])
            ->get();

        $totalTunggakan = Payment::whereIn('house_id', $houseIds)
            ->where('status', 'unpaid')
            ->where(function ($q) {
                $q->where('type', '!=', 'monthly')
                    ->orWhere('period_year', '<', now()->year)
                    ->orWhere(function ($q2) {
                        $q2->where('period_year', now()->year)->where('period_month', '<=', now()->month);
                    });
            })
            ->sum('amount');

        return view('resident.dashboard.index', compact('currentPeriodPayments', 'otherPendingPayments', 'totalTunggakan'));
    }
}
