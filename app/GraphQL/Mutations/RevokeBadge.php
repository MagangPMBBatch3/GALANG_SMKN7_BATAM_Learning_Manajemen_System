<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\UserBadge;

final readonly class RevokeBadge
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $userId = $args['user_id'];
        $badgeId = $args['badge_id'];

        $userBadge = UserBadge::where('user_id', $userId)->where('badge_id', $badgeId)->first();
        if ($userBadge) {
            $userBadge->delete();
            return true;
        }

        return false;
    }
}
