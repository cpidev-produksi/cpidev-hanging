<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    private array $keys = ['view','upload','edit','delete','restore'];
    private string $group = 'shfi';

    public function index(Request $request)
    {
        $roles = Role::query()->orderBy('name')->get();

        $roleId = $request->query('role_id') ?: ($roles->first()->id ?? null);
        $selectedRole = $roleId ? Role::find($roleId) : null;

        $current = [];
        if ($selectedRole) {
            $rows = RolePermission::query()
                ->where('role_id', $selectedRole->id)
                ->where('group', $this->group)
                ->get();
            foreach ($rows as $r) $current[$r->key] = (bool) $r->allowed;
        }

        return view('account.role-permissions', [
            'roles' => $roles,
            'selectedRole' => $selectedRole,
            'keys' => $this->keys,
            'group' => $this->group,
            'current' => $current,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role_id' => ['required','integer','exists:roles,id'],
            'perm' => ['array'],
            'perm.*' => ['in:'.implode(',', $this->keys)],
        ]);

        $roleId = (int) $data['role_id'];
        $checked = collect($data['perm'] ?? [])->values()->all();

        foreach ($this->keys as $k) {
            RolePermission::updateOrCreate(
                ['role_id' => $roleId, 'group' => $this->group, 'key' => $k],
                ['allowed' => in_array($k, $checked, true)]
            );
        }

        return redirect()
            ->route('account.role-permissions.index', ['role_id' => $roleId])
            ->with('status', 'Permissions berhasil disimpan.');
    }
}
