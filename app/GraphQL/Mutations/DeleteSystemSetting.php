<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\SystemSetting;

final readonly class DeleteSystemSetting
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        $key = $args['key'];

        $setting = SystemSetting::where('key', $key)->first();
        if ($setting) {
            $setting->delete();
            return true;
        }

        return false;
    }
}
