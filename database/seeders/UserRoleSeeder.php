<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full access to the application',
            ]
        );

        $instructorRole = Role::firstOrCreate(
            ['name' => 'instructor'],
            [
                'display_name' => 'Instructor',
                'description' => 'Can create and manage courses',
            ]
        );

        $studentRole = Role::firstOrCreate(
            ['name' => 'student'],
            [
                'display_name' => 'Student',
                'description' => 'Can enroll and take courses',
            ]
        );

        // Create admin user if it doesn't exist
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@maxcourse.local'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => 'password', // Will be hashed by mutator
                'is_active' => true,
            ]
        );

        // Attach admin role
        if (!$adminUser->roles()->where('role_id', $adminRole->id)->exists()) {
            $adminUser->roles()->attach($adminRole);
        }

        $this->command->info('Roles and admin user created successfully!');
        $this->command->table(['Name', 'Email', 'Username', 'Password'], [
            ['Admin', 'admin@maxcourse.local', 'admin', 'password'],
        ]);
    }
}
