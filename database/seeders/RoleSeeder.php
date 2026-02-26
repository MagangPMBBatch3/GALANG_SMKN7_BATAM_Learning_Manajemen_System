<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full access to all system features'],
            ['name' => 'instructor', 'display_name' => 'Instructor', 'description' => 'Can create and manage courses'],
            ['name' => 'student', 'display_name' => 'Student', 'description' => 'Can enroll in courses and access learning materials'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
