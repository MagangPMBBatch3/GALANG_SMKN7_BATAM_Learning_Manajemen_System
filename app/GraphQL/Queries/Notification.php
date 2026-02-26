<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

final readonly class Notification
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $userId = $args['user_id'];
        return \App\Models\Notification::where('user_id', $userId)
            ->orderBy('sent_at', 'desc');
    }
}
