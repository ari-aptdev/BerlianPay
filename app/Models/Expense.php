<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'period_month',
        'period_year',
        'expense_date',
        'category',
        'amount',
        'description',
        'recorded_by_admin_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'expense_date' => 'date',
        ];
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_admin_id');
    }
}
