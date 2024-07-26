<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
  
//    public function __construct()
//     {
//         $this->middleware('permission:Listar Roles',['only'=>['index','show']]);
//         $this->middleware('permission:Guardar Roles',['only'=>['store','create']]);
//         $this->middleware('permission:Actualizar Roles',['only'=>['update','edit']]);
//         $this->middleware('permission:Eliminar Roles',['only'=>['destroy']]);
//     }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::get();
        return view('role-permission.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('role-permission.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'unique:roles,name']]);
        Role::create(['name' => $request->name]);
        return redirect('roles')->with('status', 'Rol creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return view('role-permission.roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => ['required', 'string', 'unique:roles,name,' . $role->id]]);
        $role->update(['name' => $request->name]);
        return redirect('roles')->with('status', 'Rol actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect('roles')->with('status', 'Rol eliminar exitosamente');
    }

    public function addPermissionToRole($roleId)
    {
        $permissions = Permission::get();
        $role = Role::findOrFail($roleId);
        $rolePermissions = DB::table('role_has_permissions')
                    ->where('role_has_permissions.role_id',$role->id)
                    ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
                    ->all();
        return view('role-permission.roles.add-permissions', compact('role', 'permissions','rolePermissions'));
    }

    public function givePermissionToRole(Request $request, Role $role){
        $request->validate(['permission'=>'required']);
        $role->syncPermissions($request->permission);
        return redirect()->back()->with('status','Permissions added to role');
    }
}
