<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Enrollment;

final readonly class UnenrollCourse
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $enrollmentId = $args['enrollment_id'];

        $enrollment = Enrollment::findOrFail($enrollmentId);

        // Delete the enrollment
        $enrollment->delete();

        return true;
    }
}
