<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\SystemSetting;

final readonly class SystemSettings
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): array
    {
        return SystemSetting::all()->toArray();
    }
}
