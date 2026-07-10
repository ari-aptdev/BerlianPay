@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-medium text-slate-900">Pencatatan pembayaran</h2>
    <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">
        <i class="ti ti-plus"></i> Catat pembayaran
    </a>
</div>

<form method="GET" class="flex flex-wrap gap-3 mb-4">
    <select name="status" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <option value="">Semua status</option>
        <option value="paid" @selected(request('status') === 'paid')>Lunas</option>
        <option value="unpaid" @selected(request('status') === 'unpaid')>Belum bayar</option>
    </select>
    <select name="month" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <option value="">Semua bulan</option>
        @for ($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" @selected((int) request('month') === $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
        @endfor
    </select>
    <input type="number" name="year" value="{{ request('year') }}" placeholder="Tahun" class="w-24 rounded-lg border border-slate-200 px-3 py-2 text-sm">
    <button class="bg-slate-100 text-slate-600 text-sm px-4 py-2 rounded-lg">Filter</button>
</form>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Rumah</th>
                <th class="text-left px-4 py-2 font-normal">Periode</th>
                <th class="text-left px-4 py-2 font-normal">Nominal</th>
                <th class="text-left px-4 py-2 font-normal">Status</th>
                <th class="text-left px-4 py-2 font-normal">Bukti</th>
                <th class="text-left px-4 py-2 font-normal">TTD</th>
                <th class="text-right px-4 py-2 font-normal">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">{{ $payment->house->fullLabel() }}</td>
                    <td class="px-4 py-2.5">{{ $payment->periodLabel() }}</td>
                    <td class="px-4 py-2.5">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5">
                        @if ($payment->status === 'paid')
                            <span class="bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-md">Lunas</span>
                        @else
                            <span class="bg-red-50 text-red-700 text-xs px-2.5 py-1 rounded-md">Belum bayar</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5">
                        @if ($payment->proof_image)
                            <a href="{{ Storage::url($payment->proof_image) }}" target="_blank" class="text-brand-600 hover:underline">Lihat</a>
                        @else
                            <span class="text-slate-300">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5">
                        @if ($payment->signature)
                            <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs px-2 py-1 rounded-md">
                                <i class="ti ti-signature text-xs"></i> Ada
                            </span>
                        @else
                            <span class="text-slate-300">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-right space-x-2">
                        <a href="{{ route('admin.payments.edit', $payment) }}" class="text-brand-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('Hapus data pembayaran ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-slate-400">Belum ada data pembayaran.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $payments->links() }}</div>
@endsection
