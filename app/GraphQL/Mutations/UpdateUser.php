<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;

final readonly class UpdateUser
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): User
    {
        $userId = $args['id'];
        $input = $args['input'];

        $user = User::findOrFail($userId);

        $updateData = [];
        if (isset($input['email'])) {
            $updateData['email'] = $input['email'];
        }
        if (isset($input['name'])) {
            $updateData['name'] = $input['name'];
        }
        if (isset($input['username'])) {
            $updateData['username'] = $input['username'];
        }
        if (isset($input['bio'])) {
            $updateData['bio'] = $input['bio'];
        }
        if (isset($input['avatar_url'])) {
            $updateData['avatar_url'] = $input['avatar_url'];
        }
        if (isset($input['is_active'])) {
            $updateData['is_active'] = $input['is_active'];
        }

        $user->update($updateData);

        return $user->load('roles');
    }
}
