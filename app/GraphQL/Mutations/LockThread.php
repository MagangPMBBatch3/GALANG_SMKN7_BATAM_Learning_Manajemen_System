<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\ForumThread;

final readonly class LockThread
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): ForumThread
    {
        $threadId = $args['id'];

        $thread = ForumThread::findOrFail($threadId);
        $thread->is_locked = true;
        $thread->save();

        return $thread;
    }
}
