<?php

namespace App\Policies;

use App\Models\House;
use App\Models\User;

class HousePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->canAccess('houses', 'view');
    }

    /**
     * Admin dengan akses 'view' boleh lihat semua rumah.
     * Warga hanya boleh melihat rumah miliknya sendiri — ini pengecekan utama
     * yang mencegah warga mengakses data rumah lain lewat manipulasi URL.
     */
    public function view(User $user, House $house): bool
    {
        if ($user->isAdmin()) {
            return $user->canAccess('houses', 'view');
        }

        return $user->isWarga() && $user->ownsHouse($house);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() && $user->canAccess('houses', 'edit');
    }

    public function update(User $user, House $house): bool
    {
        return $user->isAdmin() && $user->canAccess('houses', 'edit');
    }

    public function delete(User $user, House $house): bool
    {
        return $user->isAdmin() && $user->canAccess('houses', 'edit');
    }
}
