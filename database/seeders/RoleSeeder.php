<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'user']);
    
        // Define the specific permissions you want to assign
        $permissions = [
            'user-list',
            'borrow-list',
            'rating-list',
            'user-edit',
            'user-delete'
        ];
        
        // Fetch only these specific permissions
        $permissions = Permission::whereIn('name', $permissions)->get();
        
        // Assign these permissions to the role
        $role->syncPermissions($permissions);
    }
}
