<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         \App\Models\User::factory(4)->create();
        Task::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            PermissionTableSeeder::class,
            CreateAdminUserSeeder::class
        ]);
    }
}
