<?php

namespace App\Policies;

use App\Models\House;
use App\Models\User;

class HousePolicy
{
    /**
     * Admin bebas kelola semua rumah.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Warga hanya boleh melihat rumah miliknya sendiri.
     * Ini adalah pengecekan utama yang mencegah warga mengakses
     * data rumah lain lewat manipulasi URL (mis. /resident/houses/7).
     */
    public function view(User $user, House $house): bool
    {
        return $user->isWarga() && $user->ownsHouse($house);
    }

    public function create(User $user): bool
    {
        return false; // hanya admin, sudah ditangani di before()
    }

    public function update(User $user, House $house): bool
    {
        return false;
    }

    public function delete(User $user, House $house): bool
    {
        return false;
    }
}
