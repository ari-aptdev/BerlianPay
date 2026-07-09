<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderLog extends Model
{
    protected $fillable = [
        'house_id',
        'payment_id',
        'channel',
        'reminder_type',
        'sent_date',
        'sent_at',
        'status',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'sent_date' => 'date',
            'sent_at' => 'datetime',
        ];
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
