<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\User as UserModel;

final readonly class Users
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // Handle paginated users query
        $first = $args['first'] ?? 15;
        $page = $args['page'] ?? 1;

        $query = UserModel::with('roles')->whereNull('deleted_at');

        // Apply search filter if provided
        if (isset($args['search']) && !empty($args['search'])) {
            $query->search($args['search']);
        }

        $paginator = $query->paginate($first, ['*'], 'page', $page);
        
        // Return the paginator instance - Lighthouse will handle wrapping
        // The @paginate directive should convert this properly
        return $paginator;
    }
}
