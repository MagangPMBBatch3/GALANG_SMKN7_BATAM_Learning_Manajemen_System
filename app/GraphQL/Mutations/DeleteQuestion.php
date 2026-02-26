<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Question;

final readonly class DeleteQuestion
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $questionId = $args['id'];

        $question = Question::findOrFail($questionId);
        $question->delete();

        return true;
    }
}
