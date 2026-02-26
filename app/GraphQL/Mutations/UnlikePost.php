<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\PostLike;

final readonly class UnlikePost
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $postId = $args['post_id'];
        $userId = $args['user_id'];

        $like = PostLike::where('post_id', $postId)->where('user_id', $userId)->first();
        if ($like) {
            $like->delete();
            return true;
        }

        return false;
    }
}
