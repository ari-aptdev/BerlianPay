@extends('layouts.admin')

@section('content')
<h2 class="text-lg font-medium text-slate-900 mb-6">Log reminder</h2>

<form method="GET" class="flex flex-wrap gap-3 mb-4">
    <select name="channel" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <option value="">Semua channel</option>
        <option value="email" @selected(request('channel') === 'email')>Email</option>
        <option value="whatsapp" @selected(request('channel') === 'whatsapp')>WhatsApp</option>
    </select>
    <select name="status" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <option value="">Semua status</option>
        <option value="sent" @selected(request('status') === 'sent')>Berhasil</option>
        <option value="failed" @selected(request('status') === 'failed')>Gagal</option>
    </select>
    <button class="bg-slate-100 text-slate-600 text-sm px-4 py-2 rounded-lg">Filter</button>
</form>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Rumah</th>
                <th class="text-left px-4 py-2 font-normal">Tipe</th>
                <th class="text-left px-4 py-2 font-normal">Channel</th>
                <th class="text-left px-4 py-2 font-normal">Waktu kirim</th>
                <th class="text-left px-4 py-2 font-normal">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">{{ $log->house->fullLabel() }}</td>
                    <td class="px-4 py-2.5">
                        {{ match($log->reminder_type) {
                            'h_minus_3' => 'H-3 jatuh tempo',
                            'h_day' => 'Hari-H',
                            default => 'Susulan tunggakan',
                        } }}
                    </td>
                    <td class="px-4 py-2.5 capitalize">{{ $log->channel }}</td>
                    <td class="px-4 py-2.5">{{ $log->sent_at?->format('d-m-Y H:i') }}</td>
                    <td class="px-4 py-2.5">
                        @if ($log->status === 'sent')
                            <span class="bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-md">Berhasil</span>
                        @else
                            <span class="bg-red-50 text-red-700 text-xs px-2.5 py-1 rounded-md" title="{{ $log->failure_reason }}">Gagal</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Belum ada reminder yang terkirim.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
