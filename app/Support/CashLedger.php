<?php

namespace App\Support;

use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;

/**
 * Buku Kas dengan saldo berjalan (running balance).
 *
 * - Kategori 'general' (Kas IPL): pemasukan OTOMATIS dari pembayaran IPL warga
 *   yang lunas (nominal penuh), pengeluaran dicatat manual oleh admin.
 * - Kategori 'security': SEPENUHNYA manual — pemasukan maupun pengeluaran
 *   dicatat sendiri oleh admin/pengurus, TIDAK diambil otomatis dari
 *   pembayaran IPL warga sama sekali.
 *
 * Saldo awal bulan = saldo akhir dari SEMUA transaksi sebelum bulan ini.
 * Tiap baris: saldo_akhir = saldo_sebelumnya + masuk - keluar.
 */
class CashLedger
{
    public static function build(int $month, int $year, string $category = 'general'): array
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = (clone $periodStart)->endOfMonth()->endOfDay();

        if ($category === 'general') {
            $incomeBefore = Payment::where('status', 'paid')->where('paid_at', '<', $periodStart)->sum('amount');
        } else {
            $incomeBefore = Expense::where('category', 'security')->where('type', 'income')
                ->where('expense_date', '<', $periodStart->toDateString())->sum('amount');
        }

        $expenseBefore = Expense::where('category', $category)->where('type', 'expense')
            ->where('expense_date', '<', $periodStart->toDateString())
            ->sum('amount');

        $startingBalance = $incomeBefore - $expenseBefore;

        // ---- Transaksi bulan ini ----
        if ($category === 'general') {
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
        } else {
            $incomeEntries = Expense::where('category', 'security')->where('type', 'income')
                ->whereBetween('expense_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                ->get()
                ->map(fn ($e) => [
                    'date' => $e->expense_date ?? $e->created_at,
                    'description' => $e->description,
                    'masuk' => $e->amount,
                    'keluar' => 0,
                    'model' => $e,
                ]);
        }

        $expenseEntries = Expense::where('category', $category)->where('type', 'expense')
            ->whereBetween('expense_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->get()
            ->map(fn ($e) => [
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
