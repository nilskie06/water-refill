<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'group' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create($data);
        return response()->json($permission, 201);
    }

    public function show(Permission $permission)
    {
        return response()->json($permission->load('roles'));
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255|unique:permissions,name,' . $permission->id,
            'group' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $permission->update($data);
        return response()->json($permission);
    }

    public function destroy(Permission $permission)
    {
        $permission->roles()->detach();
        $permission->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
