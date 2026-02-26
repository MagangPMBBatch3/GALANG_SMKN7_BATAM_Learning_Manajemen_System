<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Enrollment;
use App\Models\CourseReview;
use App\Models\Payment;

final readonly class CourseStats
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): array
    {
        $courseId = $args['course_id'];

        $enrollments = Enrollment::where('course_id', $courseId)->get();
        $reviews = CourseReview::where('course_id', $courseId)->get();
        $payments = Payment::where('course_id', $courseId)->where('status', 'paid')->get();

        $totalStudents = $enrollments->count();
        $totalEnrollments = $enrollments->count();
        $averageRating = $reviews->avg('rating') ?? 0;
        $totalReviews = $reviews->count();
        $completionRate = $enrollments->where('status', 'completed')->count() / max($totalEnrollments, 1) * 100;
        $revenue = $payments->sum('amount');

        return [
            'total_students' => $totalStudents,
            'total_enrollments' => $totalEnrollments,
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews,
            'completion_rate' => $completionRate,
            'revenue' => $revenue,
        ];
    }
}
