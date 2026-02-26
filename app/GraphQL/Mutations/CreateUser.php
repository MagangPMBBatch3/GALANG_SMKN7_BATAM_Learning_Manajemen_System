<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final readonly class CreateUser
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): User
    {
        $input = $args['input'];

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'username' => $input['username'] ?? null,
            'bio' => $input['bio'] ?? null,
            'avatar_url' => $input['avatar_url'] ?? null,
            'is_active' => true,
        ]);

        // Assign roles if provided
        if (isset($input['role_ids']) && is_array($input['role_ids'])) {
            $user->roles()->attach($input['role_ids']);
        } else {
            // Default to student role if no roles specified
            $studentRole = \App\Models\Role::where('name', 'student')->first();
            if ($studentRole) {
                $user->roles()->attach($studentRole->id);
            }
        }

        return $user->load('roles');
    }
}
