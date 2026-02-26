<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Wishlist;

final readonly class RemoveFromWishlist
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $courseId = $args['course_id'];
        $userId = auth()->id();

        $wishlistItem = Wishlist::where('user_id', $userId)->where('course_id', $courseId)->first();
        if ($wishlistItem) {
            $wishlistItem->delete();
            return true;
        }

        return false;
    }
}
