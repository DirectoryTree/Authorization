# Authorization

[![Build Status](https://img.shields.io/travis/larapacks/authorization/master.svg?style=flat-square)](https://travis-ci.org/larapacks/authorization)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/larapacks/authorization/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/larapacks/authorization/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/larapacks/authorization.svg?style=flat-square)](https://packagist.org/packages/larapacks/authorization)
[![Latest Stable Version](https://img.shields.io/packagist/v/larapacks/authorization.svg?style=flat-square)](https://packagist.org/packages/larapacks/authorization)
[![License](https://img.shields.io/packagist/l/larapacks/authorization.svg?style=flat-square)](https://packagist.org/packages/larapacks/authorization)

An easy, native role / permission management system for Laravel.

Authorization automatically adds your database permissions and roles to the `Illuminate\Auth\Access\Gate`, this means
that you can utilize native laravel policies and methods for authorization.

## Installation

Insert Authorization in your `composer.json` file:

```json
"larapacks/authorization": "1.0.*"
```

Then run `composer update`.

Insert the service provider in your `config/app.php` file:

```php
Larapacks\Authorization\AuthorizationServiceProvider::class,
```

Once that's complete, publish the migrations using:

```php
php artisan vendor:publish --tag="authorization"
```

Then run `php artisan migrate`.

Once you've done the migrations, create the following two models and insert the relevant trait:

The Role model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Larapacks\Authorization\Traits\RolePermissionsTrait;

class Role extends Model
{
    use RolePermissionsTrait;

    /**
     * The roles table.
     *
     * @var string
     */
    protected $table = 'roles';
}
```

The permission model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Larapacks\Authorization\Traits\PermissionRolesTrait;

class Permission extends Model
{
    use PermissionRolesTrait;

    /**
     * The permissions table.
     *
     * @var string
     */
    protected $table = 'permissions';
}
```

Now insert the `Larapacks\Authorization\Traits\UserRolesTrait` onto your `App\Models\User` model:

```php
namespace App\Models;

use Larapacks\Authorization\Traits\UserRolesTrait;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model
{
    use Authenticatable, Authorizable, CanResetPassword, UserRolesTrait;
    
    /**
     * The users table.
     *
     * @var string
     */
    protected $table = 'users';
}
```

You're all set!

## Usage

Create a permission:

```php
$createUsers = new Permission();

$createUsers->name = 'users.create';
$createUsers->label = 'Create Users';

$createUsers->save();
```

Grant the permission to a role:

```php
$administrator = new Role();

$administrator->name = 'administrator';
$administrator->label = 'Admin';

$administrator->save();

$administrator->grant($createUsers);
```

Now assign the role to the user:

```php
auth()->user()->assignRole($administrator);
```

Perform authorization like so:

```php
if (auth()->user()->hasPermission('users.create')) {
    
}
```

You can also create user specific permissions:

```php
$createUsers = new Permission();

$createUsers->name = 'users.create';
$createUsers->label = 'Create Users';

$createUsers->save();

$user->permissions()->save($createUsers);

// Using the permissions name.
if ($user->hasPermission('users.create')) {
    //
}

// Using the permissions model.
if ($user->hasPermission($createUsers)) {
    //
}
```

Or using Laravel's native authorization methods such as the `Gate` facade:

```php
if (Gate::allows('users.edit')) {
    //
}
```

Or by using Laravel's native `AuthorizesRequests` trait methods in your controllers:

```php
public function index()
{
    $this->authorize('users.index');
    
    // User can access index.
}
```

Or by using Laravel's native `can` method on the user:

```php
if (auth()->user()->can('users.index')) {
    // This user can access the index.
}
```


Or by using Laravel's native `@can` directive in your views:

```php
@can('users.index')
    <!-- This user can access the index. -->
@endcan
```

Checking for multiple permissions:

```php
if (auth()->user()->hasPermissions(['users.create', 'users.edit'])) {
    // This user has both creation and edit rights.
} else {
    // It looks like the user doesn't have one of the specified permissions.
}
```

Checking if the user has any permissions:

```php
if (auth()->user()->hasAnyPermissions(['users.create', 'users.edit', 'users.destroy'])) {
    // This user either has create, edit or destroy permissions.
} else {
    // It looks like the user doesn't have any of the specified permissions.
}
```

Checking if the user has a role:

```php
if (auth()->user()->hasRole('administrator')) {
    // This user is an administrator.
} else {
    // It looks like the user isn't an administrator.
}
```

Checking if the user has specified roles:

```php
if (auth()->user()->hasRoles(['administrator', 'member'])) {
    // This user is an administrator and a member.
} else {
    // It looks like the user isn't an administrator or member.
}
```

Checking if the user has any specified roles:

```php
if (auth()->user()->hasAnyRoles(['administrator', 'member', 'guest'])) {
    // This user is either an administrator, member or guest.
} else {
    // It looks like the user doesn't have any of these roles.
}
```

### Middleware

Authorization includes two useful middleware classes you can utilize for your routes.

Insert them into your `app/Http/Kernel.php`:

```php
/**
 * The application's route middleware.
 *
 * These middleware may be assigned to groups or used individually.
 *
 * @var array
 */
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'permission' => \Larapacks\Authorization\Middleware\PermissionMiddleware::class, // The permission middleware
    'role' => \Larapacks\Authorization\Middleware\RoleMiddleware::class, // The role middleware
];
```

Once you've done that, you can start using them.

> **Note**: When a user does not meet the requirements using the middleware,
> an `Illuminate\Contracts\Validation\UnauthorizedException` is thrown.

To guard a route to only allow specific permissions:

```php
Route::get('users', [
    'uses' => 'UsersController@index',
    'middleware' => 'permission:users.index',
]);

// Multiple permissions:
Route::get('users', [
    'uses' => 'UsersController@index',
    'middleware' => 'permission:users.index,users.create', // Users must have index **and** create rights to access this route.
]);
```

To guard a route to allow a specific role:

```php
Route::get('users', [
    'uses' => 'UsersController@index',
    'middleware' => 'role:administrator',
]);

// Multiple roles:
Route::get('users', [
    'uses' => 'UsersController@index',
    'middleware' => 'role:administrator,member', // Users must be an administrator **and** a member to access this route.
]);
```

### Model Specific Permissions

To create permissions for a specific model, use the models key for a unique permission name. For example:

```php
$user = User::find(1);

$permission = new Permission();

$permission->name = "users.edit.$user->id";
$permission->label = "Edit User: $user->name";

$permission->save();
```

Then validate it when editing the specific model:

```php
public function edit($id)
{
    $user = $this->user->findOrFail($id);

    // The current user must have permission to edit this specific user.
    $this->authorize("users.edit.$user->id");
    
    return view('users.edit', compact('user'));
}
```
