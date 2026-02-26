<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Quiz;

final readonly class DeleteQuiz
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $quizId = $args['id'];

        $quiz = Quiz::findOrFail($quizId);
        $quiz->delete();

        return true;
    }
}
