<!-- readme.md -->

<p align="center">
<img src="https://github.com/DirectoryTree/Authorization/blob/master/art/logo.svg" width="400">
</p>

<p align="center">
<a href="https://github.com/DirectoryTree/Authorization/actions"><img src="https://img.shields.io/github/actions/workflow/status/directorytree/authorization/run-tests.yml?branch=master&style=flat-square"></a>
<a href="https://packagist.org/packages/DirectoryTree/Authorization"><img src="https://img.shields.io/packagist/dt/DirectoryTree/Authorization.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/DirectoryTree/Authorization"><img src="https://img.shields.io/packagist/v/DirectoryTree/Authorization.svg?style=flat-square"/></a>
<a href="https://packagist.org/packages/DirectoryTree/Authorization"><img src="https://img.shields.io/github/license/DirectoryTree/Authorization.svg?style=flat-square"/></a>
</p>

<p align="center">
An easy, native role / permission management system for Laravel.
</p>

## Index

-   [Installation](#installation)
    -   [Migration Customization](#migration-customization)
    -   [Model Customization](#model-customization)
-   [Usage](#usage)
-   [Checking Permissions & Roles](#checking-permissions--roles)
-   [Caching](#caching)
-   [Gate Registration](#gate-registration)
-   [Middleware](#middleware)
-   [Testing](#running-tests)

## Installation

To get started, install Authorization via the Composer package manager:

    composer require directorytree/authorization

The Authorization service provider registers its own database migration directory
with the framework, so you should migrate your database after installing the
package. The Authorization migrations will create the tables your
application needs to store roles and permissions:

    php artisan migrate

Now insert the `DirectoryTree\Authorization\Traits\Authorizable` onto your `App\Models\User` model:

```php
<?php

namespace App\Models;

use DirectoryTree\Authorization\Traits\Authorizable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Authorizable;

    // ...
}
```

You can now perform user authorization.

### Migration Customization

If you would not like to use Authorization's default migrations, you should call the
`Authorization::ignoreMigrations` method in the `register` method of your
`AppServiceProvider`. You may export the default migrations using
`php artisan vendor:publish --tag=authorization-migrations`.

```php
use DirectoryTree\Authorization\Authorization;

/**
 * Register any application services.
 *
 * @return void
 */
public function register()
{
    Authorization::ignoreMigrations();
}
```

### Model Customization

By default, the `App\Models\User` class is registered as the authorizable user model.

You're free to extend the models used internally by Authorization, or create your own.

Instruct Authorization to use your own models via the `Authorization` class in your `AuthServiceProvider`:

```php
use DirectoryTree\Authorization\Authorization;

/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{
    $this->registerPolicies();

    Authorization::useUserModel(\App\Models\User::class);
    Authorization::useRoleModel(\App\Models\Role::class);
    Authorization::usePermissionModel(\App\Models\Permission::class);
}
```

Be sure to add the relevant traits for each of your custom models:

**Role Model**:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DirectoryTree\Authorization\Traits\ManagesPermissions;

class Role extends Model
{
    use ManagesPermissions;
}
```

**Permission Model**:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DirectoryTree\Authorization\Traits\HasUsers;
use DirectoryTree\Authorization\Traits\HasRoles;
use DirectoryTree\Authorization\Traits\ClearsCachedPermissions;

class Permission extends Model
{
    use HasUsers, HasRoles, ClearsCachedPermissions;
}
```

## Usage

Authorization uses native Laravel relationships, so there's no need to learn a new API if you don't want to.

> Due to Authorization's trait based implementation, all of Authorization's functionality can be overridden or extended with you own implementation.

### Managing Roles & Permissions

Create a permission:

```php
use DirectoryTree\Authorization\Permission;

$createUsers = Permission::create([
    'name' => 'users.create',
    'label' => 'Create Users',
]);
```

Create a role:

```php
use DirectoryTree\Authorization\Role;

$admin = Role::create([
    'name' => 'administrator',
    'label' => 'Admin',
]);
```

Grant the permission to a role:

```php
$admin->permissions()->save($createUsers);
```

Now assign the role to the user:

```php
$user->roles()->save($admin);
```

You may also use the `grant()` or `revoke()` method on a `Role` model:

```php
// Using the permission's name:
$admin->grant('users.create');

// Using a permission model:
$admin->grant($permission);

// Granting multiple permissions:
$admin->grant(['users.create', 'users.edit']);

// Granting a collection of models:
$admin->grant(Permission::all());

// Using a mix of models and permission name:
$admin->grant([$createUsers, 'users.edit']);
```

You may also sync a role's permissions using the `grantOnly()` method:

```php
// All permissions will be removed except, except for the given:
$admin->grantOnly('users.create');
```

```php
// Using the permission's name:
$admin->revoke('users.create');

// Using a permission model:
$admin->revoke($permission);

// Revoking multiple permissions:
$admin->revoke(['users.create', 'users.edit']);

// Revoking a collection of models:
$admin->revoke(Permission::all());

// Using a mix of models and permission name:
$admin->revoke([$createUsers, 'users.edit']);
```

You may also detach all permissions from a role using `revokeAll()`:

```php
$admin->revokeAll();
```

### Managing Users & Permissions

You can also create user specific permissions:

```php
$createUsers = Permission::create([
    'name' => 'users.create',
    'label' => 'Create Users',
]);

$user->permissions()->save($createUsers);
```

As with roles, may also use the `grant()` or `revoke()` method on an authorizable `User` model:

```php
// Using the permission's name:
$user->grant('users.create');

// Using a permission model:
$user->grant($permission);

// Granting multiple permissions:
$user->grant(['users.create', 'users.edit']);

// Granting a collection of models:
$user->grant(Permission::all());

// Using a mix of models and permission name:
$user->grant([$createUsers, 'users.edit']);
```

You may also sync a users' permissions using the `grantOnly()` method:

```php
// All permissions will be removed except, except for the given:
$user->grantOnly('users.create');
```

```php
// Using the permission's name:
$user->revoke('users.create');

// Using a permission model:
$user->revoke($permission);

// Granting multiple permissions:
$user->revoke(['users.create', 'users.edit']);

// Granting a collection of models:
$user->revoke(Permission::all());

// Using a mix of models and permission name:
$user->revoke([$createUsers, 'users.edit']);
```

### Checking Permissions & Roles

Using Laravel's native `can()` method:

```php
if (Auth::user()->can('users.create')) {
    // This user can create other users.
}
```

Using Laravel's native `authorize()` method in your controllers:

```php
public function create()
{
    $this->authorize('users.create');

    User::create(['...']);
}
```

Using Laravel's native `Gate` facade:

```php
if (Gate::allows('users.create')) {
    //
}
```

Using Laravel's native `@can` directive in your views:

```blade
@can('users.create')
    <!-- This user can create other users. -->
@endcan
```

### Checking Permissions & Roles (Using Authorization Package Methods)

Checking for permission:

```php
// Using the permissions name.
if ($user->hasPermission('users.create')) {
    //
}

// Using the permissions model.
if ($user->hasPermission($createUsers)) {
    //
}
```

Checking for multiple permissions:

```php
if ($user->hasPermissions(['users.create', 'users.edit'])) {
    // This user has both creation and edit rights.
} else {
    // The user doesn't have one of the specified permissions.
}
```

Checking if the user has any permissions:

```php
if ($user->hasAnyPermissions(['users.create', 'users.edit', 'users.destroy'])) {
    // This user either has create, edit or destroy permissions.
} else {
    // The user doesn't have any of the specified permissions.
}
```

Checking if the user has a role:

```php
if ($user->hasRole('administrator')) {
    // This user is an administrator.
} else {
    // The user isn't an administrator.
}
```

Checking if the user has specified roles:

```php
if ($user->hasRoles(['administrator', 'member'])) {
    // This user is an administrator and a member.
} else {
    // The user isn't an administrator or member.
}
```

Checking if the user has any specified roles:

```php
if ($user->hasAnyRoles(['administrator', 'member', 'guest'])) {
    // This user is either an administrator, member or guest.
} else {
    // The user doesn't have any of these roles.
}
```

### Caching

By default all permissions are cached to prevent them from being retrieved on every request.

This cache is automatically flushed when permissions are created, updated, or deleted.

If you would like to disable the cache, call `Authorization::disablePermissionCache` in your `AuthServiceProvider`:

```php
use DirectoryTree\Authorization\Authorization;

/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{
    $this->registerPolicies();

    Authorization::disablePermissionCache();
}
```

#### Cache Key

By default, the permission cache key is `authorization.permissions`.

To alter the cache key, call `Authorization::cacheKey` in your `AuthServiceProvider`:

```php
use DirectoryTree\Authorization\Authorization;

/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{
    $this->registerPolicies();

    Authorization::cacheKey('my-key');
}
```

#### Cache Expiry

By default, the permission cache will expire daily.

To alter this expiry date, call `Authorization::cacheExpiresIn` in your `AuthServiceProvider`:

```php
use DirectoryTree\Authorization\Authorization;

/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{
    $this->registerPolicies();

    Authorization::cacheExpiresIn(now()->addWeek());
}
```

### Gate Registration

By default all permissions you create are registered in Laravel's Gate.

If you would like to disable this, call `Authorization::disableGateRegistration` in your `AuthServiceProvider`:

```php
use DirectoryTree\Authorization\Authorization;

/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{
    $this->registerPolicies();

    Authorization::disableGateRegistration();
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

    // The role middleware:
    'role' => \DirectoryTree\Authorization\Middleware\RoleMiddleware::class,

    // The permission middleware:
    'permission' => \DirectoryTree\Authorization\Middleware\PermissionMiddleware::class,
];
```

Once you've added them, you can start using them.

> **Note**: When a user does not meet the requirements using the middleware, a 403 HTTP exception is thrown.

To guard a route to only allow specific permissions:

```php
Route::get('users', [
    'uses' => 'UsersController@index',
    'middleware' => 'permission:users.index',
]);

// Multiple permissions:
Route::get('users', [
    'uses' => 'UsersController@index',
    // Users must have index **and** create rights to access this route.
    'middleware' => 'permission:users.index,users.create',
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
    // Users must be an administrator **and** a member to access this route.
    'middleware' => 'role:administrator,member',
]);
```

### Running Tests

To run your applications tests, **you must** instantiate the `PermissionRegistrar`
inside your `TestCase::setUp()` method **before** running your
tests for permissions to register properly:

```php
use DirectoryTree\Authorization\PermissionRegistrar;
```

```php
protected function setUp() : void
{
    parent::setUp();

    app(PermissionRegistrar::class)->register();
}
```
