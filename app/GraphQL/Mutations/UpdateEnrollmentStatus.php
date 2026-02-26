<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Enrollment;

final readonly class UpdateEnrollmentStatus
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): Enrollment
    {
        $enrollmentId = $args['id'];
        $status = $args['status'];

        $enrollment = Enrollment::findOrFail($enrollmentId);
        $enrollment->status = $status;
        $enrollment->save();

        return $enrollment;
    }
}
