<?php

namespace App\Mail;

use App\Models\House;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public House $house,
        public string $reminderType,
        public int $totalTunggakan,
        public string $periodLabel,
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->reminderType) {
            'h_minus_3' => "Pengingat: IPL {$this->periodLabel} akan jatuh tempo",
            'h_day' => "Jatuh tempo hari ini: IPL {$this->periodLabel}",
            default => 'Pengingat tunggakan IPL BerlianPay',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-reminder',
            with: [
                'house' => $this->house,
                'reminderType' => $this->reminderType,
                'totalTunggakan' => $this->totalTunggakan,
                'periodLabel' => $this->periodLabel,
            ],
        );
    }
}
