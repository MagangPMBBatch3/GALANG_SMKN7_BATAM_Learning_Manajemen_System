<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

final readonly class UserBadge
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $userId = $args['user_id'];
        return \App\Models\UserBadge::where('user_id', $userId)
            ->with('badge')
            ->orderBy('awarded_at', 'desc');
    }
}
