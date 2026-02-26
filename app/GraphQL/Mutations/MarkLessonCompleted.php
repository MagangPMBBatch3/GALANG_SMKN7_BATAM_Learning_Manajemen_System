<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\LessonProgress;

final readonly class MarkLessonCompleted
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): LessonProgress
    {
        $lessonId = $args['lesson_id'];
        $courseId = $args['course_id'];
        $userId = auth()->id();

        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $userId, 'lesson_id' => $lessonId, 'course_id' => $courseId],
            ['is_completed' => true, 'completed_at' => now()]
        );

        if (!$progress->is_completed) {
            $progress->is_completed = true;
            $progress->completed_at = now();
            $progress->save();
        }

        return $progress;
    }
}
