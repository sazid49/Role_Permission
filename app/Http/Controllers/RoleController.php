<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Actions\Role\CreateRole;
use App\Actions\Role\UpdateRole;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\RoleFormRequest;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
     {
        $this->middleware(['permission:role list'])->only('index');   
        $this->middleware(['permission:create role'])->only('create');   
        $this->middleware(['permission:edit role'])->only('edit');   
        $this->middleware(['permission:update role'])->only('update');   
        $this->middleware(['permission:delete role'])->only('destroy');   
     }

    public function index()
    {   
        $roles = Role::query()->with('permissions')->latest()->get();
        return view('role.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();
        // $permission_groups = User::getPermissionGroup();

        return view('role.create',compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
         $request->validate([
           'name'=>'required|unique:roles,name',
         ]);

        $role =  Role::create(['name'=>$request->name,'guard_name'=>'web']);
        $role->syncPermissions($request->permissions);
        // CreateRole::create($request);

        session()->flash('success', 'Role Created!');
        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::query()->with('permissions')->find($id);
        $permissions = Permission::all();
        $roleId = $role->permissions()->pluck('id')->toArray();
        // dd($roleId);
        return view('role.edit',compact('role','permissions','roleId'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Role $role)
    {
       $request->validate([
           'name'=>"required|unique:roles,name,$role->id",
         ]);
        
         $name = $request->name;
        $role->update(['name'=>$name]);
        $role->syncPermissions($request->permissions);
        session()->flash('success','Role Update success');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        // abort_if(!userCan('role.delete'), 403);
        try {
            if (!is_null($role)) {
                $role->delete();
            }
            session()->flash('success', 'Role Deleted!');
            return back();
        } catch (\Throwable $th) {
            session()->flash('Error', 'Something is wrong');
            return back();
        }
    }
}
