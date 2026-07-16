<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\ResidentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentConfirmationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:payment_confirmations,view'])->only(['index', 'viewProof']);
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

    /**
     * Tampilkan bukti transfer langsung dari disk (bukan lewat symlink public/storage).
     * Ini lebih robust — tetap jalan meskipun `storage:link` belum/gagal dijalankan.
     */
    public function viewProof(Payment $payment)
    {
        abort_unless($payment->proof_image, 404);
        abort_unless(Storage::disk('public')->exists($payment->proof_image), 404, 'File bukti transfer tidak ditemukan di server. Kemungkinan hilang karena redeploy (lihat catatan storage persistent di README).');

        return Storage::disk('public')->response($payment->proof_image);
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

        $this->notifyResidents($payment, 'payment_approved',
            'Pembayaran IPL Disetujui',
            "IPL {$payment->periodLabel()} untuk {$payment->house->fullLabel()} sudah divalidasi dan dinyatakan LUNAS."
        );

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

        $this->notifyResidents($payment, 'payment_rejected',
            'Konfirmasi Pembayaran Ditolak',
            "IPL {$payment->periodLabel()} untuk {$payment->house->fullLabel()} ditolak. Alasan: {$request->rejection_reason}"
        );

        return back()->with('success', 'Konfirmasi ditolak, warga bisa mengajukan ulang.');
    }

    protected function notifyResidents(Payment $payment, string $type, string $title, string $message): void
    {
        foreach ($payment->house->residents as $resident) {
            ResidentNotification::notify($resident->id, $type, $title, $message, route('resident.payments.show', $payment));
        }
    }
}
