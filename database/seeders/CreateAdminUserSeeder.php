<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'firstName' => 'Ayham' ,
            'lastName'  => 'ibrahim', 
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'role'    => 'Admin'
        ]);
    
        $role = Role::create(['name' => 'Admin']);
     
        $permissions = Permission::all();
   
        $role->syncPermissions($permissions);
     
        $user->assignRole($role);
    }
}
