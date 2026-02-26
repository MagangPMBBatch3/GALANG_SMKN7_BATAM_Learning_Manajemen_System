<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--name=Admin} {--email=admin@maxcourse.local} {--password=password}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create an admin user for the application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email '{$email}' already exists!");
            return 1;
        }

        // Create the user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password, // Will be hashed automatically via mutator
            'username' => strtolower(str_replace(' ', '_', $name)),
            'is_active' => true,
        ]);

        // Ensure admin role exists
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full access to the application',
            ]
        );

        // Attach admin role to user
        $user->roles()->attach($adminRole->id);

        $this->info('Admin user created successfully!');
        $this->table(['Field', 'Value'], [
            ['ID', $user->id],
            ['Name', $user->name],
            ['Email', $user->email],
            ['Username', $user->username],
            ['Role', 'Administrator'],
        ]);

        return 0;
    }
}
