<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isAdmin()) {
            return $user->canAccess('payments', 'view');
        }

        return true; // resident controller tetap filter query by house milik user
    }

    /**
     * Admin dengan akses 'view' boleh lihat semua pembayaran.
     * Warga hanya boleh melihat detail pembayaran pada rumah yang jadi miliknya.
     */
    public function view(User $user, Payment $payment): bool
    {
        if ($user->isAdmin()) {
            return $user->canAccess('payments', 'view');
        }

        return $user->isWarga() && $user->ownsHouse($payment->house);
    }

    /**
     * Admin dengan akses 'edit' boleh catat pembayaran manual.
     * Warga TIDAK BOLEH input pembayaran langsung jadi 'paid' — cuma bisa
     * mengajukan konfirmasi (lihat ResidentPaymentConfirmationController).
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() && $user->canAccess('payments', 'edit');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->isAdmin() && $user->canAccess('payments', 'edit');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->isAdmin() && $user->canAccess('payments', 'edit');
    }
}
