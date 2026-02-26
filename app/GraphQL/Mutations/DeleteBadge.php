<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Badge;

final readonly class DeleteBadge
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $badgeId = $args['id'];

        $badge = Badge::findOrFail($badgeId);
        $badge->delete();

        return true;
    }
}
