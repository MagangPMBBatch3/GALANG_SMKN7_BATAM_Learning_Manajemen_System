<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Notification;

final readonly class MarkAllNotificationsRead
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $userId = $args['user_id'];

        Notification::where('user_id', $userId)->where('is_read', false)->update(['is_read' => true]);

        return true;
    }
}
