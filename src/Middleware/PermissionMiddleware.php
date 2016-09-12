<?php

namespace Larapacks\Authorization\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * Run the request filter.
     *
     * @param Request $request
     * @param Closure $next
     * @param array   $permissions
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissions = null)
    {
        $args = func_get_args();

        if ($args > 3) {
            // If we've been given more than one permission, we
            // need to retrieve all of them from the method.
            $permissions = array_slice($args, 2);
        }

        $permissions = collect($permissions);

        if (!$request->user()->hasPermissions($permissions)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
