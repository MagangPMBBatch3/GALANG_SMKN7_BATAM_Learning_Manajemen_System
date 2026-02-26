<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

final readonly class Points
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $userId = $args['user_id'];
        return \App\Models\Point::where('user_id', $userId)
            ->orderBy('created_at', 'desc');
    }
}
