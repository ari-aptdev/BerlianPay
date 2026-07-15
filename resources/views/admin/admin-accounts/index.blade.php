@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-lg font-medium text-slate-900">Akun Admin & RBAC</h2>
    @if (auth()->user()->canAccess('admin_accounts', 'edit'))
        <a href="{{ route('admin.admin-accounts.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">
            <i class="ti ti-plus"></i> Buat akun admin
        </a>
    @endif
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Nama</th>
                <th class="text-left px-4 py-2 font-normal">Email</th>
                <th class="text-left px-4 py-2 font-normal">Level akses</th>
                <th class="text-left px-4 py-2 font-normal">Status</th>
                <th class="text-right px-4 py-2 font-normal">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($admins as $admin)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">{{ $admin->name }} @if($admin->id === auth()->id()) <span class="text-xs text-slate-400">(kamu)</span> @endif</td>
                    <td class="px-4 py-2.5">{{ $admin->email }}</td>
                    <td class="px-4 py-2.5">
                        @php
                            $accessLabels = ['full' => 'Full Access', 'read_only' => 'Read Only', 'custom' => 'Custom'];
                        @endphp
                        <span class="bg-brand-50 text-brand-700 text-xs px-2.5 py-1 rounded-md">
                            {{ $accessLabels[$admin->admin_access_type ?? 'full'] }}
                        </span>
                    </td>
                    <td class="px-4 py-2.5">
                        @if ($admin->is_active)
                            <span class="bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-md">Aktif</span>
                        @else
                            <span class="bg-slate-100 text-slate-500 text-xs px-2.5 py-1 rounded-md">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-right space-x-2">
                        @if (auth()->user()->canAccess('admin_accounts', 'edit'))
                            <a href="{{ route('admin.admin-accounts.edit', $admin) }}" class="text-brand-600 hover:underline">Edit</a>
                            @if ($admin->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.admin-accounts.toggle-active', $admin) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button class="text-slate-500 hover:underline">{{ $admin->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </form>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Belum ada akun admin lain.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $admins->links() }}</div>
@endsection
