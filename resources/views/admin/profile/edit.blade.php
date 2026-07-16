@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-6">Profil Saya</h2>

<div class="bg-white rounded-xl border border-slate-200 p-6 max-w-lg">
    <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
        @csrf @method('PATCH')

        <div>
            <label class="block text-sm text-slate-600 mb-1.5">Nama</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm text-slate-600 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2 border-t border-slate-100">
            <p class="text-sm font-medium text-slate-700 mb-2">Ganti password (opsional)</p>
            <input type="password" name="password" placeholder="Password baru" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm mb-2">
            <input type="password" name="password_confirmation" placeholder="Konfirmasi password baru" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="bg-slate-50 rounded-lg px-4 py-3 text-xs text-slate-500">
            Level akses: <strong>{{ ['full' => 'Full Access', 'read_only' => 'Read Only', 'custom' => 'Custom'][auth()->user()->admin_access_type ?? 'full'] }}</strong>
            @if (auth()->user()->is_super_admin)
                &middot; <span class="text-brand-600 font-medium">Super Admin</span>
            @endif
            <br>Buat ubah level akses, hubungi Super Admin — ini gak bisa diatur sendiri.
        </div>

        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg">Simpan perubahan</button>
    </form>
</div>
@endsection
