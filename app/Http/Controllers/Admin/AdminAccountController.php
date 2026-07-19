<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:admin_accounts,view'])->only(['index']);
        $this->middleware(['permission:admin_accounts,edit'])->only(['create', 'store', 'edit', 'update', 'toggleActive']);
    }

    public function index()
    {
        $admins = User::where('role', 'admin')->orderBy('name')->paginate(15);

        return view('admin.admin-accounts.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admin-accounts.create', ['modules' => User::MODULES]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'admin_access_type' => ['required', Rule::in(['full', 'read_only', 'custom'])],
            'permissions' => ['nullable', 'array'],
        ]);

        $permissions = null;
        if ($validated['admin_access_type'] === 'custom') {
            // permissions dikirim dari form sebagai: permissions[houses][]=view&permissions[houses][]=edit dst
            $permissions = $request->input('permissions', []);
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'admin_access_type' => $validated['admin_access_type'],
            'permissions' => $permissions,
            'is_active' => true,
        ]);

        return redirect()->route('admin.admin-accounts.index')->with('success', 'Akun admin berhasil dibuat.');
    }

    public function edit(User $adminAccount)
    {
        abort_unless($adminAccount->isAdmin(), 404);

        return view('admin.admin-accounts.edit', ['admin' => $adminAccount, 'modules' => User::MODULES]);
    }

    public function update(Request $request, User $adminAccount)
    {
        abort_unless($adminAccount->isAdmin(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($adminAccount->id)],
            'admin_access_type' => ['required', Rule::in(['full', 'read_only', 'custom'])],
            'permissions' => ['nullable', 'array'],
        ]);

        $permissions = null;
        if ($validated['admin_access_type'] === 'custom') {
            $permissions = $request->input('permissions', []);
        }

        $adminAccount->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'admin_access_type' => $validated['admin_access_type'],
            'permissions' => $permissions,
        ]);

        return redirect()->route('admin.admin-accounts.index')->with('success', 'Akun admin berhasil diperbarui.');
    }

    public function toggleActive(User $adminAccount)
    {
        abort_unless($adminAccount->isAdmin(), 404);
        abort_if($adminAccount->id === auth()->id(), 403, 'Tidak bisa nonaktifkan akun sendiri.');

        $adminAccount->update(['is_active' => ! $adminAccount->is_active]);

        return back()->with('success', $adminAccount->is_active ? 'Akun admin diaktifkan.' : 'Akun admin dinonaktifkan.');
    }
}
