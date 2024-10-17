<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'user_forceDelte',
            'user_trash',
            'user_restore',

            'task-list',
            'task-create',
            'task-edit',
            'task-delete',
            'task_forceDelte',
            'task_trash',
            'task_restore',

            'update_status',
            'assign_task',
            'reAssign_task',
            'upload_attachment',
            'add_comment',
            'task_reports'

         ];
        foreach ($permissions as $permission) {

            Permission::create(['name' => $permission]);
        }
    }
}
