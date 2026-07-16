@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-6">Tarif IPL</h2>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-slate-700">Non-Rukem</p>
            <span class="bg-slate-100 text-slate-600 text-xs px-2.5 py-1 rounded-md">Total Rp {{ number_format(array_sum($nonRukemBreakdown), 0, ',', '.') }}</span>
        </div>
        <table class="w-full text-sm">
            @foreach ($nonRukemBreakdown as $label => $amount)
                <tr class="border-t border-slate-100">
                    <td class="py-2 text-slate-500">{{ $label }}</td>
                    <td class="py-2 text-right">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-medium text-slate-700">Rukem</p>
            <span class="bg-brand-50 text-brand-700 text-xs px-2.5 py-1 rounded-md">Total Rp {{ number_format(array_sum($rukemBreakdown), 0, ',', '.') }}</span>
        </div>
        <table class="w-full text-sm">
            @foreach ($rukemBreakdown as $label => $amount)
                <tr class="border-t border-slate-100">
                    <td class="py-2 text-slate-500">{{ $label }}</td>
                    <td class="py-2 text-right">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>

@if (auth()->user()->canAccess('ipl_rates', 'edit'))
<div class="bg-white rounded-xl border border-slate-200 p-6 max-w-lg">
    <p class="text-sm font-medium text-slate-700 mb-4">Edit Nominal Tiap Komponen</p>
    <form method="POST" action="{{ route('admin.ipl-rates.update') }}" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm text-slate-600 mb-1.5">Iuran Kas (Rukem & Non-Rukem)</label>
            <input type="number" name="ipl_kas" value="{{ old('ipl_kas', $components['kas']) }}" min="0" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('ipl_kas') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1.5">Iuran Kebersihan (Rukem & Non-Rukem)</label>
            <input type="number" name="ipl_kebersihan" value="{{ old('ipl_kebersihan', $components['kebersihan']) }}" min="0" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('ipl_kebersihan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1.5">Iuran Keamanan (Rukem & Non-Rukem)</label>
            <input type="number" name="ipl_keamanan" value="{{ old('ipl_keamanan', $components['keamanan']) }}" min="0" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('ipl_keamanan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm text-slate-600 mb-1.5">Iuran Rukem (khusus Rukem)</label>
            <input type="number" name="ipl_rukem_tambahan" value="{{ old('ipl_rukem_tambahan', $components['rukem_tambahan']) }}" min="0" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('ipl_rukem_tambahan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="pt-3 border-t border-slate-100">
            <label class="block text-sm text-slate-600 mb-1.5">Biaya pendaftaran anggota baru Rukem</label>
            <input type="number" name="rukem_registration_fee" value="{{ old('rukem_registration_fee', $registrationFee) }}" min="0" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('rukem_registration_fee') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-slate-400 mt-1">Dikenakan sekali di awal saat warga baru daftar dan pilih ikut Rukem.</p>
        </div>

        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg w-full sm:w-auto">Simpan Tarif</button>
    </form>
</div>
@endif
@endsection
