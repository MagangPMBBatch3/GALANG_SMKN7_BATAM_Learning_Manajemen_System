<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Choice;

final readonly class DeleteChoice
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $choiceId = $args['id'];

        $choice = Choice::findOrFail($choiceId);
        $choice->delete();

        return true;
    }
}
