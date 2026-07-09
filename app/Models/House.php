<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'block',
        'house_number',
        'owner_name',
        'phone',
        'type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_house');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function reminderLogs(): HasMany
    {
        return $this->hasMany(ReminderLog::class);
    }

    public function fullLabel(): string
    {
        return "Blok {$this->block}-{$this->house_number}";
    }

    /**
     * Ambil tarif IPL yang berlaku untuk tipe rumah ini pada tanggal tertentu.
     */
    public function currentRate(?string $onDate = null): ?IplRate
    {
        return IplRate::where('house_type', $this->type)
            ->where('effective_date', '<=', $onDate ?? now()->toDateString())
            ->orderByDesc('effective_date')
            ->first();
    }
}
