<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\SystemSetting;

final readonly class UpdateSystemSetting
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): SystemSetting
    {
        $input = $args['input'];

        $setting = SystemSetting::updateOrCreate(
            ['key' => $input['key']],
            ['value' => $input['value']]
        );

        return $setting;
    }
}
