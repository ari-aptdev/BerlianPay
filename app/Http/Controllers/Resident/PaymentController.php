<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Daftar riwayat pembayaran, dibatasi HANYA untuk rumah milik user login.
     * Query di-scope dengan whereIn house_id agar warga tidak bisa lihat data warga lain
     * meskipun mencoba mengubah parameter apapun di request.
     */
    public function index(Request $request)
    {
        $houseIds = $request->user()->houses()->pluck('houses.id');

        $payments = Payment::with('house')
            ->whereIn('house_id', $houseIds)
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->paginate(12);

        return view('resident.payments.index', compact('payments'));
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
}
