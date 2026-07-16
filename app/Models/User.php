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
        'pending_block',
        'pending_house_number',
        'pending_wants_rukem',
        'is_active',
        'is_super_admin',
        'admin_access_type',
        'permissions',
        'reminder_email_enabled',
        'reminder_wa_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Daftar modul menu admin yang bisa diatur aksesnya untuk RBAC custom.
     * key => label yang ditampilkan di UI.
     */
    public const MODULES = [
        'houses' => 'Data Warga & Rumah',
        'ipl_rates' => 'Tarif IPL',
        'payments' => 'Pembayaran',
        'payment_confirmations' => 'Konfirmasi Pembayaran Warga',
        'reports' => 'Laporan',
        'residents' => 'Akun Warga',
        'admin_accounts' => 'Akun Admin & RBAC',
        'settings' => 'Pengaturan Reminder',
        'reminder_logs' => 'Log Reminder',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_super_admin' => 'boolean',
            'pending_wants_rukem' => 'boolean',
            'permissions' => 'array',
            'reminder_email_enabled' => 'boolean',
            'reminder_wa_enabled' => 'boolean',
        ];
    }

    public function houses(): BelongsToMany
    {
        return $this->belongsToMany(House::class, 'user_house');
    }

    public function residentNotifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ResidentNotification::class);
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
     * Cek apakah admin ini punya akses ke modul tertentu.
     * $action: 'view' atau 'edit'.
     * - Akun admin lama / access_type null / 'full' -> selalu true (super admin, backward compatible)
     * - 'read_only' -> hanya lolos untuk action 'view'
     * - 'custom' -> cek array permissions[$module] berisi $action
     */
    public function canAccess(string $module, string $action = 'view'): bool
    {
        if (! $this->isAdmin()) {
            return false;
        }

        // Modul RBAC (kelola akun admin lain) HANYA boleh diakses super admin,
        // gak peduli admin_access_type-nya full/read_only/custom sekalipun.
        // Ini mencegah admin biasa (Ketua RT, sekretaris, dst) ngedit akses dirinya sendiri atau admin lain.
        if ($module === 'admin_accounts') {
            return $this->is_super_admin;
        }

        $type = $this->admin_access_type ?? 'full';

        if ($type === 'full') {
            return true;
        }

        if ($type === 'read_only') {
            return $action === 'view';
        }

        // custom
        $allowed = $this->permissions[$module] ?? [];

        return in_array($action, $allowed, true);
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
