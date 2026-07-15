<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisteredResidentController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'nik' => ['required', 'digits:16', 'unique:users,nik'],
        ], [
            'nik.unique' => 'NIK ini sudah terdaftar. Kalau ini akun kamu, hubungi admin buat reset password.',
        ]);

        $username = User::generateUsername($validated['name'], $validated['nik']);
        $password = Str::password(10, symbols: false);

        $resident = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'nik' => $validated['nik'],
            'username' => $username,
            'password' => Hash::make($password),
            'role' => 'warga',
            'is_active' => false, // wajib diaktifkan admin dulu
        ]);

        // Coba auto-match ke rumah yang NIK pemiliknya sama persis (sudah diinput admin sebelumnya)
        $matchedHouse = House::where('nik', $validated['nik'])->first();
        if ($matchedHouse) {
            $resident->houses()->sync([$matchedHouse->id]);
        }

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
