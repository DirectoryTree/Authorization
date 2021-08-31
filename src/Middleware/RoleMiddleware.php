<?php

namespace DirectoryTree\Authorization\Middleware;

class RoleMiddleware extends AuthorizationMiddleware
{
    /**
     * Determine if the user has the required roles to access the route.
     *
     * @param \App\User  $user
     * @param array|null $roles
     *
     * @return bool
     */
    protected function authorize($user, $roles = null)
    {
        return $user->hasRoles($roles);
    }
}
