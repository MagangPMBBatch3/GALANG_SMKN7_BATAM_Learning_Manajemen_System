<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\SystemSetting as SystemSettingModel;

final readonly class SystemSetting
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): ?array
    {
        $key = $args['key'];

        $setting = SystemSettingModel::where('key', $key)->first();
        return $setting ? $setting->toArray() : null;
    }
}
