<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\ForumThread;

final readonly class DeleteThread
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $threadId = $args['id'];

        $thread = ForumThread::findOrFail($threadId);
        $thread->delete();

        return true;
    }
}
