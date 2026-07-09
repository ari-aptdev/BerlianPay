<?php

namespace App\Jobs;

use App\Mail\PaymentReminderMail;
use App\Models\House;
use App\Models\ReminderLog;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public House $house,
        public string $reminderType, // h_minus_3 | h_day | overdue_followup
        public int $totalTunggakan,
        public string $periodLabel,
        public ?int $paymentId = null,
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        foreach ($this->house->residents as $resident) {
            if ($resident->reminder_email_enabled) {
                $this->sendChannel('email', $resident->email, $whatsApp);
            }

            if ($resident->reminder_wa_enabled && $resident->phone) {
                $this->sendChannel('whatsapp', $resident->phone, $whatsApp);
            }
        }
    }

    protected function sendChannel(string $channel, string $target, WhatsAppService $whatsApp): void
    {
        // Cegah kirim dobel: cek log hari ini untuk rumah + channel + tipe reminder yang sama
        $alreadySent = ReminderLog::where('house_id', $this->house->id)
            ->where('channel', $channel)
            ->where('reminder_type', $this->reminderType)
            ->whereDate('sent_date', today())
            ->exists();

        if ($alreadySent) {
            return;
        }

        $status = 'sent';
        $failureReason = null;

        try {
            if ($channel === 'email') {
                Mail::to($target)->send(new PaymentReminderMail(
                    $this->house,
                    $this->reminderType,
                    $this->totalTunggakan,
                    $this->periodLabel,
                ));
            } else {
                $message = "Pengingat IPL {$this->house->fullLabel()}: periode {$this->periodLabel}, tunggakan Rp ".number_format($this->totalTunggakan, 0, ',', '.');
                $sent = $whatsApp->send($target, $message);
                if (! $sent) {
                    $status = 'failed';
                    $failureReason = 'Provider WA belum dikonfigurasi atau gagal mengirim.';
                }
            }
        } catch (\Throwable $e) {
            $status = 'failed';
            $failureReason = $e->getMessage();
            Log::error('[SendPaymentReminderJob] Gagal kirim reminder.', ['error' => $e->getMessage()]);
        }

        ReminderLog::create([
            'house_id' => $this->house->id,
            'payment_id' => $this->paymentId,
            'channel' => $channel,
            'reminder_type' => $this->reminderType,
            'sent_date' => today(),
            'sent_at' => now(),
            'status' => $status,
            'failure_reason' => $failureReason,
        ]);
    }
}
