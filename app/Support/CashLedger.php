<?php

namespace App\Support;

use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;

/**
 * Buku Kas: gabungan pemasukan (pembayaran IPL yang lunas) dan pengeluaran,
 * diurutkan kronologis, dengan saldo berjalan (running balance) per baris.
 *
 * Saldo awal bulan = saldo akhir dari SEMUA transaksi sebelum bulan ini.
 * Tiap baris: saldo_akhir = saldo_sebelumnya + masuk - keluar.
 */
class CashLedger
{
    public static function build(int $month, int $year): array
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = (clone $periodStart)->endOfMonth()->endOfDay();

        // Saldo awal = akumulasi semua transaksi SEBELUM tanggal 1 bulan ini
        $incomeBefore = Payment::where('status', 'paid')
            ->where('paid_at', '<', $periodStart)
            ->sum('amount');

        $expenseBefore = Expense::where('expense_date', '<', $periodStart->toDateString())
            ->sum('amount');

        $startingBalance = $incomeBefore - $expenseBefore;

        // Transaksi bulan ini, digabung & diurutkan kronologis
        $incomeEntries = Payment::with('house')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->get()
            ->map(fn ($p) => [
                'date' => $p->paid_at,
                'description' => $p->house->fullLabel().' - '.$p->displayLabel(),
                'masuk' => $p->amount,
                'keluar' => 0,
            ]);

        $expenseEntries = Expense::where(function ($q) use ($periodStart, $periodEnd) {
            $q->whereBetween('expense_date', [$periodStart->toDateString(), $periodEnd->toDateString()]);
        })->get()->map(fn ($e) => [
            'date' => $e->expense_date ?? $e->created_at,
            'description' => $e->description,
            'masuk' => 0,
            'keluar' => $e->amount,
            'model' => $e,
        ]);

        $entries = $incomeEntries->concat($expenseEntries)
            ->sortBy('date')
            ->values();

        $runningBalance = $startingBalance;
        $ledger = $entries->map(function ($entry) use (&$runningBalance) {
            $runningBalance = $runningBalance + $entry['masuk'] - $entry['keluar'];
            $entry['saldo_akhir'] = $runningBalance;

            return $entry;
        });

        $totalMasuk = $ledger->sum('masuk');
        $totalKeluar = $ledger->sum('keluar');
        $endingBalance = $startingBalance + $totalMasuk - $totalKeluar;

        return [
            'entries' => $ledger,
            'startingBalance' => $startingBalance,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'endingBalance' => $endingBalance,
        ];
    }
}
