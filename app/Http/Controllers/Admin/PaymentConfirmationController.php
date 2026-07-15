<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentConfirmationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:payment_confirmations,view'])->only(['index']);
        $this->middleware(['auth', 'permission:payment_confirmations,edit'])->only(['approve', 'reject']);
    }

    public function index()
    {
        $pending = Payment::with('house')
            ->where('status', 'pending_confirmation')
            ->orderBy('confirmed_at')
            ->paginate(15);

        return view('admin.payment-confirmations.index', compact('pending'));
    }

    public function approve(Request $request, Payment $payment)
    {
        abort_unless($payment->isPendingConfirmation(), 404);

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'recorded_by_admin_id' => $request->user()->id,
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Pembayaran disetujui dan ditandai lunas.');
    }

    public function reject(Request $request, Payment $payment)
    {
        abort_unless($payment->isPendingConfirmation(), 404);

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $payment->update([
            'status' => 'unpaid',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Konfirmasi ditolak, warga bisa mengajukan ulang.');
    }
}
