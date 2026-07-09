<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'username',
        'nik',
        'is_active',
        'reminder_email_enabled',
        'reminder_wa_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'reminder_email_enabled' => 'boolean',
            'reminder_wa_enabled' => 'boolean',
        ];
    }

    public function houses(): BelongsToMany
    {
        return $this->belongsToMany(House::class, 'user_house');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }

    /**
     * Cek apakah user (warga) memiliki akses ke rumah tertentu.
     * Dipakai di Policy — jangan hanya andalkan pengecekan di Controller.
     */
    public function ownsHouse(House $house): bool
    {
        return $this->houses()->where('houses.id', $house->id)->exists();
    }

    /**
     * Generate username login warga: nama depan (lowercase) + 3 digit akhir NIK.
     * Contoh: "Joko Samudra" + NIK "3171030905980001" -> "joko001"
     * Kalau sudah ada yang sama persis, ditambahkan angka urut di belakang.
     */
    public static function generateUsername(string $name, string $nik): string
    {
        $firstName = strtolower(preg_replace('/[^a-zA-Z]/', '', strtok(trim($name), ' ')));
        $last3 = substr($nik, -3);
        $username = $firstName.$last3;

        $original = $username;
        $counter = 1;
        while (static::where('username', $username)->exists()) {
            $username = $original.$counter;
            $counter++;
        }

        return $username;
    }
}
