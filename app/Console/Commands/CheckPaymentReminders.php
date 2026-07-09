<?php

namespace App\Console\Commands;

use App\Jobs\SendPaymentReminderJob;
use App\Models\House;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Console\Command;

class CheckPaymentReminders extends Command
{
    protected $signature = 'reminders:check';

    protected $description = 'Cek jatuh tempo & tunggakan IPL harian, lalu dispatch job reminder (email/WA) ke queue';

    public function handle(): int
    {
        $emailEnabled = Setting::get('email_reminder_enabled', '1') === '1';
        $waEnabled = Setting::get('wa_reminder_enabled', '0') === '1';

        if (! $emailEnabled && ! $waEnabled) {
            $this->info('Reminder dinonaktifkan di pengaturan. Tidak ada yang dikirim.');

            return self::SUCCESS;
        }

        $dueDate = (int) Setting::get('due_date', 10);
        $hMinus = (int) Setting::get('reminder_h_minus', 3);
        $followupDates = array_map('trim', explode(',', Setting::get('followup_dates', '20,28')));

        $today = today();
        $month = $today->month;
        $year = $today->year;

        // 1. Reminder H-3 sebelum jatuh tempo
        if ($today->day === max($dueDate - $hMinus, 1)) {
            $this->dispatchForUnpaid($month, $year, 'h_minus_3');
        }

        // 2. Reminder di hari-H jatuh tempo
        if ($today->day === $dueDate) {
            $this->dispatchForUnpaid($month, $year, 'h_day');
        }

        // 3. Reminder susulan tiap tanggal yang diatur admin (misal 20 & akhir bulan)
        $isEndOfMonth = $today->day === $today->daysInMonth;
        if (in_array((string) $today->day, $followupDates, true) || ($isEndOfMonth && in_array('akhir_bulan', $followupDates, true))) {
            $this->dispatchOverdueFollowups();
        }

        $this->info('Pengecekan reminder selesai.');

        return self::SUCCESS;
    }

    protected function dispatchForUnpaid(int $month, int $year, string $type): void
    {
        $unpaidPayments = Payment::with('house.residents')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->where('status', 'unpaid')
            ->get();

        foreach ($unpaidPayments as $payment) {
            SendPaymentReminderJob::dispatch(
                $payment->house,
                $type,
                $payment->amount,
                $payment->periodLabel(),
                $payment->id,
            );
        }

        $this->info("Dispatch {$unpaidPayments->count()} reminder tipe '{$type}'.");
    }

    /**
     * Reminder susulan untuk warga yang masih nunggak, akumulasi semua bulan belum bayar.
     */
    protected function dispatchOverdueFollowups(): void
    {
        $houses = House::where('is_active', true)->with('residents')->get();

        $count = 0;

        foreach ($houses as $house) {
            $unpaid = Payment::where('house_id', $house->id)
                ->where('status', 'unpaid')
                ->get();

            if ($unpaid->isEmpty()) {
                continue;
            }

            $totalTunggakan = $unpaid->sum('amount');
            $periodLabel = $unpaid->count() > 1
                ? "{$unpaid->count()} bulan tertunggak"
                : $unpaid->first()->periodLabel();

            SendPaymentReminderJob::dispatch(
                $house,
                'overdue_followup',
                $totalTunggakan,
                $periodLabel,
            );

            $count++;
        }

        $this->info("Dispatch {$count} reminder susulan tunggakan.");
    }
}
