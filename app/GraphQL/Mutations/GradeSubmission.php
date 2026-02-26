<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\QuizSubmission;

final readonly class GradeSubmission
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): QuizSubmission
    {
        $submissionId = $args['id'];
        $score = $args['score'];

        $submission = QuizSubmission::findOrFail($submissionId);
        $submission->score = $score;
        $submission->finished_at = now();
        $submission->status = 'graded';
        $submission->save();

        return $submission;
    }
}
