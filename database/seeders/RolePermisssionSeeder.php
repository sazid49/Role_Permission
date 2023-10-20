<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;

class RolePermisssionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name'=>'admin']);

        $permissions = [
            ['name'=>'user list'],
            ['name'=>'create user'],
            ['name'=>'edit user'],
            ['name'=>'delete user'], 
            ['name'=>'role list'],
            ['name'=>'create role'],
            ['name'=>'edit role'],
            ['name'=>'delete role'],
            
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
        $role->syncPermissions(Permission::all());
        $user = User::first();
        $user->assignRole($role); 
    }
}
