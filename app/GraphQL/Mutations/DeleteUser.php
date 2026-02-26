<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;

final readonly class DeleteUser
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $userId = $args['id'];

        $user = User::findOrFail($userId);
        $user->delete();

        return true;
    }
}
