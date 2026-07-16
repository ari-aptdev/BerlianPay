@extends('layouts.resident')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-4">Profil</h2>

<form method="POST" action="{{ route('resident.profile.update') }}" class="bg-white border border-slate-200 rounded-xl p-5 space-y-4">
    @csrf @method('PATCH')

    <div>
        <label class="block text-sm text-slate-600 mb-1.5">Nama</label>
        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm text-slate-600 mb-1.5">Email</label>
        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" placeholder="Opsional, buat reminder" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm text-slate-600 mb-1.5">No. HP</label>
        <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
    </div>

    <div class="pt-2 border-t border-slate-100">
        <p class="text-sm font-medium text-slate-700 mb-2">Preferensi reminder</p>
        <label class="flex items-center justify-between py-2 text-sm">
            <span class="text-slate-600">Email</span>
            <input type="checkbox" name="reminder_email_enabled" value="1" {{ old('reminder_email_enabled', auth()->user()->reminder_email_enabled) ? 'checked' : '' }} class="rounded border-slate-300">
        </label>
        <label class="flex items-center justify-between py-2 text-sm">
            <span class="text-slate-600">WhatsApp</span>
            <input type="checkbox" name="reminder_wa_enabled" value="1" {{ old('reminder_wa_enabled', auth()->user()->reminder_wa_enabled) ? 'checked' : '' }} class="rounded border-slate-300">
        </label>
    </div>

    <div class="pt-2 border-t border-slate-100">
        <p class="text-sm font-medium text-slate-700 mb-2">Ganti password (opsional)</p>
        <input type="password" name="password" placeholder="Password baru" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm mb-2">
        <input type="password" name="password_confirmation" placeholder="Konfirmasi password baru" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
        @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white text-sm py-2.5 rounded-lg">Simpan perubahan</button>
</form>

<form method="POST" action="{{ route('logout') }}" class="mt-3">
    @csrf
    <button class="w-full flex items-center justify-center gap-2 border border-slate-200 text-red-500 text-sm py-2.5 rounded-lg">
        <i class="ti ti-logout"></i> Keluar dari akun
    </button>
</form>
@endsection
