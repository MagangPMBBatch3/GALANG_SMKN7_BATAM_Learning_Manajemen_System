<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Notification;

final readonly class DeleteNotification
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $notificationId = $args['id'];

        $notification = Notification::findOrFail($notificationId);
        $notification->delete();

        return true;
    }
}
