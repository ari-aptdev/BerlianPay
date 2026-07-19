<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResidentAccountRequest;
use App\Models\House;
use App\Models\Payment;
use App\Models\ResidentNotification;
use App\Models\User;
use App\Support\IplPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

    /**
     * Admin bikin akun warga langsung (bukan lewat registrasi mandiri) —
     * di sini rumah harus SUDAH ada duluan, dipilih dari daftar existing.
     */
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
     * Halaman approve akun warga hasil registrasi mandiri. Data Blok/No Rumah/Rukem
     * yang diisi warga saat daftar (disimpan sementara di kolom pending_*) ditampilkan
     * di sini buat dikonfirmasi/diedit admin sebelum data rumahnya BENERAN dibuat.
     */
    public function approveForm(User $resident)
    {
        abort_unless($resident->isWarga(), 404);

        return view('admin.settings.resident-approve', compact('resident'));
    }

    /**
     * Di sinilah data rumah BENERAN masuk ke tabel houses — patokan datanya
     * adalah akun warga yang sudah di-approve (bukan pas warga daftar).
     */
    public function approve(Request $request, User $resident)
    {
        abort_unless($resident->isWarga(), 404);

        $validated = $request->validate([
            'block' => ['required', 'string', 'max:5'],
            'house_number' => ['required', 'string', 'max:20'],
            'ipl_status' => ['required', Rule::in(['rukem', 'non_rukem'])],
        ]);

        $block = strtoupper($validated['block']);
        $houseNumber = str_pad($validated['house_number'], 2, '0', STR_PAD_LEFT);

        $house = House::firstOrNew(['block' => $block, 'house_number' => $houseNumber]);
        $house->owner_name = $resident->name;
        $house->phone = $resident->phone;
        $house->nik = $resident->nik;
        $house->is_active = true;

        if ($validated['ipl_status'] === 'rukem' && ! $house->isRukem()) {
            $house->rukem_joined_at = now();
        }
        $house->ipl_status = $validated['ipl_status'];
        $house->save();

        $resident->houses()->sync([$house->id]);
        $resident->update(['is_active' => true]);

        // Warga baru yang milih ikut Rukem pas daftar dikenain biaya pendaftaran sekali.
        if ($resident->pending_wants_rukem && $validated['ipl_status'] === 'rukem') {
            Payment::create([
                'house_id' => $house->id,
                'type' => 'rukem_registration',
                'period_month' => 0,
                'period_year' => now()->year,
                'amount' => IplPricing::registrationFee(),
                'status' => 'unpaid',
                'notes' => 'Biaya pendaftaran anggota baru Rukem.',
            ]);
        }

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
        // yang belum diapprove), arahkan ke halaman approve dulu.
        if (! $resident->is_active && $resident->houses()->count() === 0) {
            return redirect()->route('admin.residents.approve-form', $resident);
        }

        $resident->update(['is_active' => ! $resident->is_active]);

        return back()->with('success', $resident->is_active ? 'Akun warga diaktifkan.' : 'Akun warga dinonaktifkan.');
    }

    /**
     * Reset password warga - admin/pengurus yang nentuin sendiri password barunya.
     */
    public function resetPassword(Request $request, User $resident)
    {
        abort_unless($resident->isWarga(), 404);

        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ]);

        $resident->update(['password' => Hash::make($validated['new_password'])]);

        return back()->with('success', "Password {$resident->name} berhasil diubah.");
    }
}
