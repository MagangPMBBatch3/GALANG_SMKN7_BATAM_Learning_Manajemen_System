<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\ForumPost;

final readonly class DeletePost
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $postId = $args['id'];

        $post = ForumPost::findOrFail($postId);
        $post->delete();

        return true;
    }
}
