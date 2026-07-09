<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return true; // resident controller tetap filter query by house milik user
    }

    /**
     * Warga hanya boleh melihat detail pembayaran pada rumah yang jadi miliknya.
     */
    public function view(User $user, Payment $payment): bool
    {
        return $user->isWarga() && $user->ownsHouse($payment->house);
    }

    /**
     * Warga TIDAK BOLEH input pembayaran sendiri — ini murni pencatatan manual admin.
     */
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Payment $payment): bool
    {
        return false;
    }

    public function delete(User $user, Payment $payment): bool
    {
        return false;
    }
}
