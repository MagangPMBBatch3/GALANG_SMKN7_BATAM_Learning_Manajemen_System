<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Enrollment;
use App\Models\QuizSubmission;
use App\Models\Point;
use App\Models\UserBadge;

final readonly class UserStats
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): array
    {
        $userId = $args['user_id'];

        $enrollments = Enrollment::where('user_id', $userId)->get();
        $quizSubmissions = QuizSubmission::where('user_id', $userId)->get();
        $points = Point::where('user_id', $userId)->sum('amount');
        $badges = UserBadge::where('user_id', $userId)->count();

        $totalCoursesEnrolled = $enrollments->count();
        $totalCoursesCompleted = $enrollments->where('status', 'completed')->count();
        $totalQuizzesTaken = $quizSubmissions->count();
        $averageQuizScore = $quizSubmissions->avg('score') ?? 0;
        $totalPoints = (int)$points;
        $totalBadges = $badges;

        return [
            'total_courses_enrolled' => $totalCoursesEnrolled,
            'total_courses_completed' => $totalCoursesCompleted,
            'total_quizzes_taken' => $totalQuizzesTaken,
            'average_quiz_score' => (float)$averageQuizScore,
            'total_points' => $totalPoints,
            'total_badges' => $totalBadges,
        ];
    }
}
