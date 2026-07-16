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
        'nik',
        'ipl_status',
        'rukem_joined_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'rukem_joined_at' => 'datetime',
        ];
    }

    public function isRukem(): bool
    {
        return $this->ipl_status === 'rukem';
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
}
