<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\IplPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisteredResidentController extends Controller
{
    public function create()
    {
        return view('auth.register', ['registrationFee' => IplPricing::registrationFee()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'nik' => ['required', 'digits:16', 'unique:users,nik'],
            'block' => ['nullable', 'string', 'max:5', 'regex:/^[A-Za-z]+$/'],
            'house_number' => ['required', 'integer', 'min:1', 'max:99'],
            'wants_rukem' => ['nullable', 'boolean'],
        ], [
            'nik.unique' => 'NIK ini sudah terdaftar. Kalau ini akun kamu, hubungi admin buat reset password.',
            'block.regex' => 'Blok cuma boleh huruf, misal B.',
        ]);

        $username = User::generateUsername($validated['name'], $validated['nik']);
        $password = Str::password(10, symbols: false);

        // PENTING: data rumah (block/house_number) DAN status Rukem CUMA disimpan
        // sementara di akun user ini. Rumah baru benar-benar dibuat/masuk ke tabel
        // "Data Warga & Rumah" setelah admin approve akun ini (lihat
        // ResidentAccountController::approve()) — patokan datanya adalah akun warga.
        User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'nik' => $validated['nik'],
            'username' => $username,
            'password' => Hash::make($password),
            'role' => 'warga',
            'is_active' => false, // wajib diaktifkan admin dulu
            'pending_block' => $validated['block'] ? strtoupper($validated['block']) : null,
            'pending_house_number' => str_pad((string) $validated['house_number'], 2, '0', STR_PAD_LEFT),
            'pending_wants_rukem' => $request->boolean('wants_rukem'),
        ]);

        return redirect()->route('register.success')->with([
            'generated_username' => $username,
            'generated_password' => $password,
        ]);
    }

    public function success(Request $request)
    {
        abort_unless($request->session()->has('generated_username'), 404);

        return view('auth.register-success', [
            'username' => $request->session()->get('generated_username'),
            'password' => $request->session()->get('generated_password'),
        ]);
    }
}
