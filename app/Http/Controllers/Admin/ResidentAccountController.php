<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResidentAccountRequest;
use App\Models\House;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResidentAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->isAdmin(), 403);

            return $next($request);
        });
    }

    public function index()
    {
        $residents = User::where('role', 'warga')->with('houses')->orderBy('name')->paginate(15);

        return view('admin.settings.residents', compact('residents'));
    }

    public function create()
    {
        $houses = House::where('is_active', true)->orderBy('block')->orderBy('house_number')->get();

        return view('admin.settings.resident-create', compact('houses'));
    }

    public function store(StoreResidentAccountRequest $request)
    {
        $resident = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'warga',
        ]);

        $resident->houses()->sync($request->house_ids);

        return redirect()->route('admin.residents.index')->with('success', 'Akun warga berhasil dibuat.');
    }

    /**
     * Aktifkan / nonaktifkan akses login warga.
     */
    public function toggleActive(User $resident)
    {
        abort_unless($resident->isWarga(), 404);

        $resident->update(['is_active' => ! $resident->is_active]);

        return back()->with('success', $resident->is_active ? 'Akun warga diaktifkan.' : 'Akun warga dinonaktifkan.');
    }

    /**
     * Reset password warga ke password acak, admin akan info manual ke warga.
     */
    public function resetPassword(User $resident)
    {
        abort_unless($resident->isWarga(), 404);

        $newPassword = Str::random(8);
        $resident->update(['password' => Hash::make($newPassword)]);

        return back()->with('success', "Password direset. Password sementara: {$newPassword}");
    }
}
