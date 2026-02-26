<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\CourseReview;

final readonly class DeleteReview
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $reviewId = $args['id'];

        $review = CourseReview::findOrFail($reviewId);
        $review->delete();

        return true;
    }
}
