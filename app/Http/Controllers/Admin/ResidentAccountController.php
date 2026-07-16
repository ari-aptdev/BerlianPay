<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResidentAccountRequest;
use App\Models\House;
use App\Models\ResidentNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResidentAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:residents,view'])->only(['index']);
        $this->middleware(['auth', 'permission:residents,edit'])->only([
            'create', 'store', 'toggleActive', 'resetPassword', 'approveForm', 'approve',
        ]);
    }

    public function index()
    {
        $residents = User::where('role', 'warga')->with('houses')->orderByRaw('is_active asc')->orderBy('name')->paginate(15);

        return view('admin.settings.residents', compact('residents'));
    }

    public function create()
    {
        $houses = House::where('is_active', true)->orderBy('block')->orderBy('house_number')->get();

        return view('admin.settings.resident-create', compact('houses'));
    }

    public function store(StoreResidentAccountRequest $request)
    {
        $username = User::generateUsername($request->name, $request->nik);

        $resident = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nik' => $request->nik,
            'username' => $username,
            'password' => Hash::make($request->password),
            'role' => 'warga',
            'is_active' => true, // dibuat langsung oleh admin, gak perlu approval lagi
        ]);

        $resident->houses()->sync($request->house_ids);

        return redirect()->route('admin.residents.index')
            ->with('success', "Akun warga berhasil dibuat. Username login: {$username}");
    }

    /**
     * Halaman approve akun warga hasil registrasi mandiri.
     * Kalau NIK-nya udah cocok otomatis ke rumah tertentu, tampilkan itu.
     * Kalau belum ada rumah yang cocok, admin pilih manual di sini.
     */
    public function approveForm(User $resident)
    {
        abort_unless($resident->isWarga(), 404);

        $houses = House::where('is_active', true)->orderBy('block')->orderBy('house_number')->get();
        $assignedHouseIds = $resident->houses->pluck('id')->toArray();

        return view('admin.settings.resident-approve', compact('resident', 'houses', 'assignedHouseIds'));
    }

    public function approve(Request $request, User $resident)
    {
        abort_unless($resident->isWarga(), 404);

        $request->validate([
            'house_ids' => ['required', 'array', 'min:1'],
            'house_ids.*' => ['exists:houses,id'],
        ]);

        $resident->houses()->sync($request->house_ids);
        $resident->update(['is_active' => true]);

        ResidentNotification::notify(
            $resident->id,
            'account_approved',
            'Akun Kamu Disetujui!',
            'Selamat datang di BerlianPay. Akun kamu sudah aktif, silakan login dan cek status pembayaran IPL kamu.',
        );

        return redirect()->route('admin.residents.index')->with('success', "Akun {$resident->name} berhasil diaktifkan.");
    }

    /**
     * Aktifkan / nonaktifkan akses login warga.
     */
    public function toggleActive(User $resident)
    {
        abort_unless($resident->isWarga(), 404);

        // Kalau belum ada rumah yang di-assign (warga hasil registrasi mandiri
        // yang NIK-nya gak auto-match), arahkan ke halaman approve dulu.
        if (! $resident->is_active && $resident->houses()->count() === 0) {
            return redirect()->route('admin.residents.approve-form', $resident);
        }

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
