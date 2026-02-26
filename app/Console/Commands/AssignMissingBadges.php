<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Enrollment;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Support\Str;

class AssignMissingBadges extends Command
{
    protected $signature   = 'badges:assign-missing';
    protected $description = 'Assign badges retroactively to users who completed courses but have no badge yet';

    public function handle()
    {
        $completedEnrollments = Enrollment::where('status', 'completed')
            ->orWhere('progress_percent', '>=', 100)
            ->with(['user', 'course'])
            ->get();

        $this->info("Found {$completedEnrollments->count()} completed enrollment(s).");

        $assigned = 0;

        foreach ($completedEnrollments as $enrollment) {
            if (!$enrollment->user || !$enrollment->course) {
                continue;
            }

            $badgeCode = 'COURSE_COMPLETED_' . $enrollment->course_id;

            $badge = Badge::firstOrCreate(
                ['code' => $badgeCode],
                [
                    'name'        => 'Lulus: ' . Str::limit($enrollment->course->title, 20),
                    'description' => 'Penghargaan atas penyelesaian kursus ' . $enrollment->course->title,
                    'icon_url'    => null,
                ]
            );

            $alreadyHas = UserBadge::where('user_id', $enrollment->user_id)
                ->where('badge_id', $badge->id)
                ->exists();

            if (!$alreadyHas) {
                UserBadge::create([
                    'user_id'    => $enrollment->user_id,
                    'badge_id'   => $badge->id,
                    'awarded_at' => $enrollment->updated_at ?? now(),
                ]);

                $this->line("  ✓ Badge '{$badge->name}' assigned to user #{$enrollment->user_id} ({$enrollment->user->name})");
                $assigned++;
            } else {
                $this->line("  - User #{$enrollment->user_id} already has badge for course #{$enrollment->course_id}");
            }
        }

        $this->info("Done! {$assigned} badge(s) assigned.");

        return Command::SUCCESS;
    }
}
