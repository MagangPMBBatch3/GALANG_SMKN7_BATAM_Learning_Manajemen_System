<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Notification;

final readonly class MarkNotificationRead
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): Notification
    {
        $notificationId = $args['id'];

        $notification = Notification::findOrFail($notificationId);
        $notification->is_read = true;
        $notification->save();

        return $notification;
    }
}
