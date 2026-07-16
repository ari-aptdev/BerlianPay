<?php

namespace App\Models;

use App\Support\IplPricing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_id',
        'type',
        'period_month',
        'period_year',
        'amount',
        'ipl_status',
        'breakdown',
        'status',
        'proof_image',
        'signature',
        'signed_at',
        'confirmed_at',
        'rejection_reason',
        'paid_at',
        'recorded_by_admin_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'signed_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'amount' => 'integer',
            'breakdown' => 'array',
        ];
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_admin_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPendingConfirmation(): bool
    {
        return $this->status === 'pending_confirmation';
    }

    public function isUnpaid(): bool
    {
        return $this->status === 'unpaid';
    }

    public function isRegistrationFee(): bool
    {
        return $this->type === 'rukem_registration';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'paid' => 'Lunas',
            'pending_confirmation' => 'Menunggu Validasi',
            default => 'Belum Bayar',
        };
    }

    public function periodLabel(): string
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return ($bulan[$this->period_month] ?? $this->period_month).' '.$this->period_year;
    }

    /**
     * Label yang ditampilkan ke user — beda buat biaya pendaftaran Rukem vs iuran bulanan biasa.
     */
    public function displayLabel(): string
    {
        return $this->isRegistrationFee() ? 'Biaya Pendaftaran Rukem' : "IPL {$this->periodLabel()}";
    }

    /**
     * Rincian komponen iuran. Kalau baris ini dibuat sebelum fitur breakdown ada
     * (data lama), hitung ulang dari tarif SEKARANG sebagai fallback.
     * Pakai method ini di view, JANGAN akses $payment->breakdown langsung
     * kalau butuh fallback (data lama breakdown-nya null).
     */
    public function resolvedBreakdown(): array
    {
        if (! empty($this->breakdown)) {
            return $this->breakdown;
        }

        if ($this->isRegistrationFee()) {
            return ['Biaya Pendaftaran Rukem' => $this->amount];
        }

        return IplPricing::breakdownFor($this->ipl_status ?? 'non_rukem');
    }
}
