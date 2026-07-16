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
            'block' => ['required', 'string', 'max:5', 'regex:/^[A-Za-z]+$/'],
            'house_number' => ['required', 'integer', 'min:1', 'max:99'],
        ], [
            'nik.unique' => 'NIK ini sudah terdaftar. Kalau ini akun kamu, hubungi admin buat reset password.',
            'block.regex' => 'Blok cuma boleh huruf, misal B.',
        ]);

        $username = User::generateUsername($validated['name'], $validated['nik']);
        $password = Str::password(10, symbols: false);

        $block = strtoupper($validated['block']);
        $houseNumber = str_pad((string) $validated['house_number'], 2, '0', STR_PAD_LEFT);

        $resident = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'nik' => $validated['nik'],
            'username' => $username,
            'password' => Hash::make($password),
            'role' => 'warga',
            'is_active' => false, // wajib diaktifkan admin dulu
        ]);

        // Otomatis bikin/hubungkan data rumah berdasarkan Blok-No Rumah yang diisi warga,
        // jadi admin TIDAK perlu lagi bikin data rumah secara manual.
        $house = House::firstOrNew(['block' => $block, 'house_number' => $houseNumber]);
        $house->owner_name = $validated['name'];
        $house->phone = $validated['phone'];
        $house->nik = $validated['nik'];
        $house->is_active = true;
        $house->save();

        $resident->houses()->sync([$house->id]);

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
