<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

final readonly class IsInWishlist
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return Wishlist::where('user_id', $user->id)
            ->where('course_id', $args['course_id'])
            ->exists();
    }
}
