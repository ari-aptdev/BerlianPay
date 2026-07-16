@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-6">Pengaturan</h2>

<div class="bg-white rounded-xl border border-slate-200 p-6 max-w-xl mb-6">
    <p class="text-sm font-medium text-slate-700 mb-4">Keamanan Sesi Login</p>
    <form method="POST" action="{{ route('admin.settings.reminder.update') }}">
        @csrf @method('PUT')

        <div class="mb-4">
            <label class="block text-sm text-slate-600 mb-1.5">Auto-logout setelah tidak aktif (menit)</label>
            <input type="number" name="session_timeout_minutes" value="{{ old('session_timeout_minutes', $settings['session_timeout_minutes']) }}" min="1" max="1440" required
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            <p class="text-xs text-slate-400 mt-1">Semua akun (admin & warga) otomatis logout kalau tidak ada aktivitas selama sekian menit.</p>
        </div>

        <input type="hidden" name="due_date" value="{{ $settings['due_date'] }}">
        <input type="hidden" name="reminder_h_minus" value="{{ $settings['reminder_h_minus'] }}">
        <input type="hidden" name="followup_dates" value="{{ $settings['followup_dates'] }}">
        <input type="hidden" name="email_reminder_enabled" value="{{ $settings['email_reminder_enabled'] }}">
        <input type="hidden" name="wa_reminder_enabled" value="{{ $settings['wa_reminder_enabled'] }}">

        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg">Simpan durasi sesi</button>
    </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-6 max-w-xl">
    <p class="text-sm font-medium text-slate-700 mb-4">Reminder Otomatis</p>
    <form method="POST" action="{{ route('admin.settings.reminder.update') }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">Tanggal jatuh tempo</label>
                <input type="number" name="due_date" value="{{ old('due_date', $settings['due_date']) }}" min="1" max="28" required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label class="block text-sm text-slate-600 mb-1.5">Reminder H- berapa hari</label>
                <input type="number" name="reminder_h_minus" value="{{ old('reminder_h_minus', $settings['reminder_h_minus']) }}" min="0" max="10" required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm text-slate-600 mb-1.5">Tanggal reminder susulan (pisahkan koma)</label>
            <input type="text" name="followup_dates" value="{{ old('followup_dates', $settings['followup_dates']) }}" placeholder="20,28"
                class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            <p class="text-xs text-slate-400 mt-1">Contoh: 20,28 artinya reminder susulan dikirim tiap tanggal 20 dan 28 untuk warga yang masih nunggak.</p>
        </div>

        <div class="space-y-3 mb-6">
            <label class="flex items-center justify-between border border-slate-200 rounded-lg px-4 py-3">
                <span class="text-sm text-slate-700">Reminder via Email</span>
                <input type="checkbox" name="email_reminder_enabled" value="1" {{ old('email_reminder_enabled', $settings['email_reminder_enabled']) ? 'checked' : '' }} class="rounded border-slate-300">
            </label>
            <label class="flex items-center justify-between border border-slate-200 rounded-lg px-4 py-3">
                <span class="text-sm text-slate-700">Reminder via WhatsApp</span>
                <input type="checkbox" name="wa_reminder_enabled" value="1" {{ old('wa_reminder_enabled', $settings['wa_reminder_enabled']) ? 'checked' : '' }} class="rounded border-slate-300">
            </label>
            <p class="text-xs text-slate-400">Reminder WA baru akan benar-benar terkirim setelah <code>WA_API_KEY</code> diisi di file <code>.env</code> (lihat README).</p>
        </div>

        <input type="hidden" name="session_timeout_minutes" value="{{ $settings['session_timeout_minutes'] }}">

        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg">Simpan pengaturan reminder</button>
    </form>
</div>
@endsection
