<?php

namespace DirectoryTree\Authorization\Middleware;

use Closure;
use Illuminate\Http\Request;

abstract class AuthorizationMiddleware
{
    /**
     * Run the request middleware.
     *
     * @param Request  $request
     * @param Closure  $next
     * @param string[] $auth
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$auth)
    {
        if (! $this->authorize($request->user(), $auth)) {
            return $this->unauthorized();
        }

        return $next($request);
    }

    /**
     * Abort the application request.
     *
     * @return void
     */
    protected function unauthorized()
    {
        abort(403, 'Unauthorized.');
    }

    /**
     * Authorize whether the user has access to the route.
     *
     * @param \App\User  $user
     * @param array|null $auth
     *
     * @return bool
     */
    abstract protected function authorize($user, $auth = null);
}
