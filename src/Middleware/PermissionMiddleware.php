<?php

namespace DirectoryTree\Authorization\Middleware;

class PermissionMiddleware extends AuthorizationMiddleware
{
    /**
     * Determine if the user has the required permission to access the route.
     *
     * @param \App\User  $user
     * @param array|null $permissions
     *
     * @return bool
     */
    protected function authorize($user, $permissions = null)
    {
        return $user->hasPermissions($permissions);
    }
}
