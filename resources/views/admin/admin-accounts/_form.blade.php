@csrf
@isset($admin) @method('PUT') @endisset

<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-1.5">Nama</label>
    <input type="text" name="name" value="{{ old('name', $admin->name ?? '') }}" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-1.5">Email (untuk login)</label>
    <input type="email" name="email" value="{{ old('email', $admin->email ?? '') }}" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

@unless(isset($admin))
<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-1.5">Password awal</label>
    <input type="text" name="password" value="{{ old('password') }}" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>
@endunless

@php
    $currentType = old('admin_access_type', $admin->admin_access_type ?? 'full');
    $currentPermissions = old('permissions', $admin->permissions ?? []);
@endphp

<div class="mb-4">
    <label class="block text-sm text-slate-600 mb-2">Level akses (Role Based Access Control)</label>
    <div class="space-y-2">
        <label class="flex items-start gap-3 border border-slate-200 rounded-lg p-3 cursor-pointer">
            <input type="radio" name="admin_access_type" value="full" onchange="toggleCustomPermissions()" {{ $currentType === 'full' ? 'checked' : '' }} class="mt-0.5">
            <span>
                <span class="block text-sm font-medium text-slate-700">Full Access</span>
                <span class="block text-xs text-slate-400">Bisa lihat dan edit semua menu, setara admin utama.</span>
            </span>
        </label>
        <label class="flex items-start gap-3 border border-slate-200 rounded-lg p-3 cursor-pointer">
            <input type="radio" name="admin_access_type" value="read_only" onchange="toggleCustomPermissions()" {{ $currentType === 'read_only' ? 'checked' : '' }} class="mt-0.5">
            <span>
                <span class="block text-sm font-medium text-slate-700">Read-Only</span>
                <span class="block text-xs text-slate-400">Cuma bisa lihat semua menu, gak bisa tambah/edit/hapus apapun.</span>
            </span>
        </label>
        <label class="flex items-start gap-3 border border-slate-200 rounded-lg p-3 cursor-pointer">
            <input type="radio" name="admin_access_type" value="custom" onchange="toggleCustomPermissions()" {{ $currentType === 'custom' ? 'checked' : '' }} class="mt-0.5">
            <span>
                <span class="block text-sm font-medium text-slate-700">Custom</span>
                <span class="block text-xs text-slate-400">Atur sendiri per menu, mana yang cuma bisa dilihat dan mana yang bisa diedit.</span>
            </span>
        </label>
    </div>
</div>

<div id="customPermissionsBox" class="mb-6 border border-slate-200 rounded-lg overflow-x-auto {{ $currentType === 'custom' ? '' : 'hidden' }}">
    <table class="w-full text-sm min-w-[420px]">
        <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left px-4 py-2 font-normal">Menu</th>
                <th class="text-center px-4 py-2 font-normal">Lihat</th>
                <th class="text-center px-4 py-2 font-normal">Edit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($modules as $key => $label)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-2.5">{{ $label }}</td>
                    <td class="px-4 py-2.5 text-center">
                        <input type="checkbox" name="permissions[{{ $key }}][]" value="view"
                            {{ in_array('view', $currentPermissions[$key] ?? []) ? 'checked' : '' }} class="rounded border-slate-300">
                    </td>
                    <td class="px-4 py-2.5 text-center">
                        <input type="checkbox" name="permissions[{{ $key }}][]" value="edit"
                            {{ in_array('edit', $currentPermissions[$key] ?? []) ? 'checked' : '' }} class="rounded border-slate-300">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="flex gap-3">
    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2.5 rounded-lg">Simpan</button>
    <a href="{{ route('admin.admin-accounts.index') }}" class="text-slate-500 text-sm px-4 py-2.5">Batal</a>
</div>

<script>
    function toggleCustomPermissions() {
        const isCustom = document.querySelector('input[name="admin_access_type"][value="custom"]').checked;
        document.getElementById('customPermissionsBox').classList.toggle('hidden', !isCustom);
    }
</script>
