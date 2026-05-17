<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:pemilik']);
    }

    public function index()
    {
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin_gudang', 'pekerja_gudang', 'kasir']);
        })->with('roles')->paginate(10);

        return view('employees.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['admin_gudang', 'pekerja_gudang', 'kasir'])->get();
        return view('employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(User $employee)
    {
        $roles = Role::whereIn('name', ['admin_gudang', 'pekerja_gudang', 'kasir'])->get();
        return view('employees.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'role' => 'required|exists:roles,name',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $employee->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $employee->syncRoles([$request->role]);

        return redirect()->route('employees.index')->with('success', 'Data karyawan diperbarui.');
    }

    public function destroy(User $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
