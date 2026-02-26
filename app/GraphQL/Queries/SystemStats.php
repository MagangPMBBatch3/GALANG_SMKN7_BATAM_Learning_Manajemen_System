<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

final readonly class SystemStats
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): array
    {
        $totalUsers = User::count();
        $totalCourses = Course::count();
        $totalEnrollments = Enrollment::count();
        $totalPayments = Payment::where('status', 'paid')->sum('amount');
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');
        $activeUsersLast30Days = User::where('last_login_at', '>=', now()->subDays(30))->count();

        return [
            'total_users' => $totalUsers,
            'total_courses' => $totalCourses,
            'total_enrollments' => $totalEnrollments,
            'total_payments' => $totalPayments,
            'total_revenue' => $totalRevenue,
            'active_users_last_30_days' => $activeUsersLast30Days,
        ];
    }
}
