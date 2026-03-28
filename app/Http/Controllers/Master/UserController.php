<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {
        $users = User::query()->with('role')->latest()->paginate(20);
        return view('master.users.index', compact('users'));
    }

    public function create() {
        $roles = Role::query()->orderBy('name')->get();
        return view('master.users.create', compact('roles'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'email' => ['required','email','max:150','unique:users,email'],
            'username' => ['required','string','max:50','unique:users,username'],
            'password' => ['required','string','min:6'],
            'role_id' => ['required','exists:roles,id'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('master.users.index')->with('status','User dibuat.');
    }

    public function edit(User $user) {
        $roles = Role::query()->orderBy('name')->get();
        return view('master.users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user) {
        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'email' => ['required','email','max:150','unique:users,email,'.$user->id],
            'username' => ['required','string','max:50','unique:users,username,'.$user->id],
            'password' => ['nullable','string','min:6'],
            'role_id' => ['required','exists:roles,id'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('master.users.index')->with('status','User diupdate.');
    }

    public function destroy(User $user) {
        $user->delete();
        return back()->with('status','User dihapus.');
    }
}
