<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

final readonly class Certificate
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $userId = $args['user_id'];
        return \App\Models\Certificate::where('user_id', $userId)
            ->with('course')
            ->orderBy('issued_at', 'desc');
    }
}
