@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-6">Buat akun warga</h2>

<div class="bg-white rounded-xl border border-slate-200 p-6 max-w-xl">
    <form method="POST" action="{{ route('admin.residents.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm text-slate-600 mb-1.5">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm text-slate-600 mb-1.5">NIK (16 digit)</label>
            <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" inputmode="numeric" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-slate-400 mt-1">Username login otomatis dibuat dari nama depan + 3 digit terakhir NIK, misal "joko001".</p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">Email (opsional, untuk reminder)</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">No. HP (untuk WA reminder)</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm text-slate-600 mb-1.5">Password awal</label>
            <input type="text" name="password" value="{{ old('password') }}" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm text-slate-600 mb-1.5">Assign ke rumah</label>
            <div class="border border-slate-200 rounded-lg p-3 max-h-52 overflow-y-auto space-y-1.5">
                @foreach ($houses as $house)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="house_ids[]" value="{{ $house->id }}" class="rounded border-slate-300">
                        {{ $house->fullLabel() }} - {{ $house->owner_name }}
                    </label>
                @endforeach
            </div>
            @error('house_ids') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg">Buat akun</button>
            <a href="{{ route('admin.residents.index') }}" class="text-slate-500 text-sm px-4 py-2.5">Batal</a>
        </div>
    </form>
</div>
@endsection
