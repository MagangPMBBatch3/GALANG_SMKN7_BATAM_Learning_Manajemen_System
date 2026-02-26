<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Carbon\Carbon;

final readonly class System
{
    public function systemStats(null $_, array $args): array
    {
        $totalUsers = User::count();
        $totalCourses = Course::count();
        $totalEnrollments = Enrollment::count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $activeUsersLast30Days = User::where('last_login_at', '>=', Carbon::now()->subDays(30))->count();

        return [
            'total_users' => $totalUsers,
            'total_courses' => $totalCourses,
            'total_enrollments' => $totalEnrollments,
            'total_revenue' => $totalRevenue,
            'active_users_last_30_days' => $activeUsersLast30Days,
        ];
    }
}
