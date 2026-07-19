<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:reports,edit']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_month' => ['required', 'integer', 'between:1,12'],
            'period_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'expense_date' => ['required', 'date'],
            'category' => ['required', Rule::in(['general', 'security'])],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'integer', 'min:0'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        // Kas IPL (general) pemasukannya tetap otomatis dari pembayaran warga,
        // jadi entry manual kategori general dipaksa selalu 'expense'.
        if ($validated['category'] === 'general') {
            $validated['type'] = 'expense';
        }

        $validated['recorded_by_admin_id'] = $request->user()->id;

        Expense::create($validated);

        return redirect()->route('admin.reports.index', [
            'month' => $validated['period_month'],
            'year' => $validated['period_year'],
        ])->with('success', 'Transaksi berhasil dicatat.');
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_date' => ['required', 'date'],
            'category' => ['required', Rule::in(['general', 'security'])],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'integer', 'min:0'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        if ($validated['category'] === 'general') {
            $validated['type'] = 'expense';
        }

        $expense->update($validated);

        return redirect()->route('admin.reports.index', [
            'month' => $expense->period_month,
            'year' => $expense->period_year,
        ])->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, Expense $expense)
    {
        $month = $expense->period_month;
        $year = $expense->period_year;

        $expense->delete();

        return redirect()->route('admin.reports.index', ['month' => $month, 'year' => $year])
            ->with('success', 'Transaksi dihapus.');
    }
}
