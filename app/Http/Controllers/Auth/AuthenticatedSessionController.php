<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // Satu form login untuk dua skema berbeda:
        // - Admin login pakai email
        // - Warga login pakai username (kombinasi nama + 3 digit akhir NIK)
        $user = User::where('email', $request->login)
            ->orWhere('username', $request->login)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'Email/username atau password salah.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'login' => 'Akun anda dinonaktifkan. Hubungi pengelola perumahan.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        // Redirect LANGSUNG ke dashboard sesuai role (bukan pakai ->intended()).
        // ->intended() bisa membawa URL yang diakses sebelumnya (mis. halaman admin)
        // dari sesi lama, sehingga warga bisa ke-redirect ke URL admin dan kena 403.
        return redirect()->to($user->isAdmin() ? route('admin.dashboard') : route('resident.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
