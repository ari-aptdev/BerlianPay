<?php

namespace App\Support;

use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;

/**
 * Buku Kas: gabungan pemasukan dan pengeluaran, diurutkan kronologis,
 * dengan saldo berjalan (running balance) per baris.
 *
 * Saldo awal bulan = saldo akhir dari SEMUA transaksi sebelum bulan ini.
 * Tiap baris: saldo_akhir = saldo_sebelumnya + masuk - keluar.
 *
 * Ada 2 kategori kas yang dipisah (kayak "dana terikat" di pembukuan):
 * - 'general' : Iuran Kas + Kebersihan + Rukem + biaya pendaftaran Rukem
 * - 'security': KHUSUS irisan "Iuran Keamanan" dari tiap pembayaran IPL warga
 * Pengeluaran (Expense) juga punya kategori sendiri, admin yang pilih pas nyatet.
 */
class CashLedger
{
    /**
     * Berapa bagian dari satu Payment yang masuk ke kategori tertentu.
     */
    protected static function incomePortion(Payment $payment, string $category): int
    {
        if ($payment->isRegistrationFee()) {
            return $category === 'general' ? $payment->amount : 0;
        }

        $breakdown = $payment->resolvedBreakdown();
        $keamanan = $breakdown['Iuran Keamanan'] ?? 0;

        if ($category === 'security') {
            return $keamanan;
        }

        return array_sum($breakdown) - $keamanan;
    }

    public static function build(int $month, int $year, string $category = 'general'): array
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = (clone $periodStart)->endOfMonth()->endOfDay();

        $paidBefore = Payment::where('status', 'paid')->where('paid_at', '<', $periodStart)->get();
        $incomeBefore = $paidBefore->sum(fn ($p) => self::incomePortion($p, $category));

        $expenseBefore = Expense::where('category', $category)
            ->where('expense_date', '<', $periodStart->toDateString())
            ->sum('amount');

        $startingBalance = $incomeBefore - $expenseBefore;

        // Transaksi bulan ini, digabung & diurutkan kronologis
        $paidThisMonth = Payment::with('house')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->get();

        $incomeEntries = $paidThisMonth
            ->map(function ($p) use ($category) {
                $amount = self::incomePortion($p, $category);
                if ($amount <= 0) {
                    return null;
                }

                $label = $category === 'security'
                    ? $p->house->fullLabel().' - Iuran Keamanan ('.$p->periodLabel().')'
                    : $p->house->fullLabel().' - '.$p->displayLabel();

                return [
                    'date' => $p->paid_at,
                    'description' => $label,
                    'masuk' => $amount,
                    'keluar' => 0,
                ];
            })
            ->filter()
            ->values();

        $expenseEntries = Expense::where('category', $category)
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
