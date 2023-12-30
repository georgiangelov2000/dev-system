<?php

namespace App\Http\Controllers;
use App\Models\AccessManagement;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\RoleManagementRequest;
use Illuminate\Http\Request;
use DB;
use Log;

class RoleManagementController extends Controller
{
    public function index(){
        return view('roles_management.index');
    }
    public function create(){
        $access_management = AccessManagement::get();
        $users = User::select('id','username')->where('role_id','0')->get();

        return view('roles_management.create',[
            'access_management' => $access_management,
            'users' => $users
        ]);
    }
    public function store(RoleManagementRequest $request)
    {
        DB::beginTransaction();
    
        try {
            $data = $request->validated();
    
            $role = new Role();
            $role->name = strtolower($data['name']);
            $role->save();
    
            // Retrieve the ID of the saved role
            $role_id = $role->id;

            // Attach permissions to the role
            $role->rolesAccessManagement()->attach($data['permissions']);
            
            // Update user roles
            User::whereIn('id', $data['users'])->update([
                'role_id' => $role_id,
            ]);
    
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Role has not been created');
        }
    
        return redirect()->route('roles.index')->with('success', 'Role has been created');
    }
    
    public function edit(Role $role){
        $role->load('userRoles','rolesAccessManagement');
        $users = User::select('id','username')->get();
        $access_management = AccessManagement::get();
        return view('roles_management.edit',compact('role','users','access_management'));
    }

    public function update(Role $role, RoleManagementRequest $request)
    {
        DB::beginTransaction();
    
        try {
            $data = $request->validated();
    
            // Update role details
            $role->name = strtolower($data['name']);
            $role->save();
    
            // Sync permissions to the role
            $role->rolesAccessManagement()->sync($data['permissions']);
    
            // Update user roles
            User::whereIn('id', $data['users'])->update([
                'role_id' => $role->id,
            ]);
    
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->with('error', 'Role has not been updated');
        }
    
        return redirect()->route('roles.index')->with('success', 'Role has been updated');
    }
    

    public function show(){

    }
}
