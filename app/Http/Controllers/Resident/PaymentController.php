<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Riwayat pembayaran Januari-Desember tahun berjalan.
     * Kalau belum ada baris Payment untuk bulan tertentu (misal bulan yang
     * belum pernah dibuka warga sehingga belum ke-generate otomatis),
     * ditampilkan sebagai placeholder "Belum ada tagihan" tanpa link.
     */
    public function index(Request $request)
    {
        $houseIds = $request->user()->houses()->pluck('houses.id');
        $year = now()->year;

        $existingPayments = Payment::with('house')
            ->whereIn('house_id', $houseIds)
            ->where('period_year', $year)
            ->get()
            ->keyBy(fn ($p) => $p->house_id.'-'.$p->period_month);

        $houses = $request->user()->houses;
        $months = [];

        foreach ($houses as $house) {
            for ($m = 1; $m <= 12; $m++) {
                $months[] = [
                    'house' => $house,
                    'month' => $m,
                    'payment' => $existingPayments->get($house->id.'-'.$m),
                ];
            }
        }

        return view('resident.payments.index', ['months' => $months, 'year' => $year]);
    }

    /**
     * Lihat detail satu pembayaran — di-guard oleh PaymentPolicy::view()
     * yang mengecek $user->ownsHouse($payment->house). Manipulasi URL
     * /resident/payments/{id} ke ID milik warga lain akan menghasilkan 403.
     */
    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);

        return view('resident.payments.show', compact('payment'));
    }

    /**
     * Download bukti pembayaran (gambar) — proteksi sama seperti show().
     */
    public function downloadProof(Payment $payment)
    {
        $this->authorize('view', $payment);

        abort_unless($payment->proof_image, 404);

        return Storage::disk('public')->download($payment->proof_image);
    }

    /**
     * Halaman upload bukti transfer buat konfirmasi mandiri pembayaran IPL.
     */
    public function confirmForm(Payment $payment)
    {
        $this->authorize('view', $payment);
        abort_unless($payment->isUnpaid(), 403, 'Pembayaran ini sudah dikonfirmasi/lunas.');

        return view('resident.payments.confirm', compact('payment'));
    }

    public function confirmSubmit(Request $request, Payment $payment)
    {
        $this->authorize('view', $payment);
        abort_unless($payment->isUnpaid(), 403, 'Pembayaran ini sudah dikonfirmasi/lunas.');

        $request->validate([
            'proof_image' => ['required', 'image', 'max:4096'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'proof_image.required' => 'Bukti transfer wajib dilampirkan.',
        ]);

        $path = $request->file('proof_image')->store('proofs', 'public');

        $payment->update([
            'proof_image' => $path,
            'notes' => $request->notes,
            'status' => 'pending_confirmation',
            'confirmed_at' => now(),
        ]);

        return redirect()->route('resident.dashboard')
            ->with('success', 'Konfirmasi pembayaran berhasil dikirim, menunggu validasi admin.');
    }
}
