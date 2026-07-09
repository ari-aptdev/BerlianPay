<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\House;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Payment::class, 'payment');
    }

    public function index(Request $request)
    {
        $payments = Payment::with('house')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->month, fn ($q) => $q->where('period_month', $request->month))
            ->when($request->year, fn ($q) => $q->where('period_year', $request->year))
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->paginate(20)
            ->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $houses = House::where('is_active', true)->orderBy('block')->orderBy('house_number')->get();

        return view('admin.payments.create', compact('houses'));
    }

    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('proof_image')) {
            $data['proof_image'] = $request->file('proof_image')->store('proofs', 'public');
        }

        if ($data['status'] === 'paid' && empty($data['paid_at'])) {
            $data['paid_at'] = now();
        }

        $data['recorded_by_admin_id'] = $request->user()->id;

        Payment::updateOrCreate(
            [
                'house_id' => $data['house_id'],
                'period_month' => $data['period_month'],
                'period_year' => $data['period_year'],
            ],
            $data
        );

        return redirect()->route('admin.payments.index')->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function edit(Payment $payment)
    {
        $houses = House::where('is_active', true)->orderBy('block')->orderBy('house_number')->get();

        return view('admin.payments.edit', compact('payment', 'houses'));
    }

    public function update(StorePaymentRequest $request, Payment $payment)
    {
        $data = $request->validated();

        if ($request->hasFile('proof_image')) {
            if ($payment->proof_image) {
                Storage::disk('public')->delete($payment->proof_image);
            }
            $data['proof_image'] = $request->file('proof_image')->store('proofs', 'public');
        }

        if ($data['status'] === 'paid' && empty($payment->paid_at) && empty($data['paid_at'])) {
            $data['paid_at'] = now();
        }

        $payment->update($data);

        return redirect()->route('admin.payments.index')->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->proof_image) {
            Storage::disk('public')->delete($payment->proof_image);
        }

        $payment->delete();

        return back()->with('success', 'Data pembayaran dihapus.');
    }
}
