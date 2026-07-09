<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIplRateRequest;
use App\Models\IplRate;

class IplRateController extends Controller
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
        $rates = IplRate::orderByDesc('effective_date')->paginate(15);

        return view('admin.ipl-rates.index', compact('rates'));
    }

    public function create()
    {
        return view('admin.ipl-rates.create');
    }

    public function store(StoreIplRateRequest $request)
    {
        IplRate::create($request->validated());

        return redirect()->route('admin.ipl-rates.index')->with('success', 'Tarif IPL berhasil ditambahkan.');
    }

    public function edit(IplRate $iplRate)
    {
        return view('admin.ipl-rates.edit', compact('iplRate'));
    }

    public function update(StoreIplRateRequest $request, IplRate $iplRate)
    {
        $iplRate->update($request->validated());

        return redirect()->route('admin.ipl-rates.index')->with('success', 'Tarif IPL berhasil diperbarui.');
    }

    public function destroy(IplRate $iplRate)
    {
        $iplRate->delete();

        return back()->with('success', 'Tarif IPL dihapus.');
    }
}
