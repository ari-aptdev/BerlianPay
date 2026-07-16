<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\IplPricing;
use Illuminate\Http\Request;

class IplRateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:ipl_rates,view'])->only(['edit']);
        $this->middleware(['auth', 'permission:ipl_rates,edit'])->only(['update']);
    }

    public function edit()
    {
        $components = IplPricing::components();
        $registrationFee = IplPricing::registrationFee();

        $nonRukemBreakdown = IplPricing::breakdownFor('non_rukem');
        $rukemBreakdown = IplPricing::breakdownFor('rukem');

        return view('admin.ipl-rates.index', compact('components', 'registrationFee', 'nonRukemBreakdown', 'rukemBreakdown'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ipl_kas' => ['required', 'integer', 'min:0'],
            'ipl_kebersihan' => ['required', 'integer', 'min:0'],
            'ipl_keamanan' => ['required', 'integer', 'min:0'],
            'ipl_rukem_tambahan' => ['required', 'integer', 'min:0'],
            'rukem_registration_fee' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, (string) $value);
        }

        return back()->with('success', 'Tarif IPL berhasil diperbarui.');
    }
}
