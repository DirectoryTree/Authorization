# Authorization

[![Build Status](https://img.shields.io/github/workflow/status/directorytree/ldaprecord/run-tests.svg?style=flat-square)](https://github.com/DirectoryTree/LdapRecord/actions)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/larapacks/authorization/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/larapacks/authorization/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/larapacks/authorization.svg?style=flat-square)](https://packagist.org/packages/larapacks/authorization)
[![Latest Stable Version](https://img.shields.io/packagist/v/larapacks/authorization.svg?style=flat-square)](https://packagist.org/packages/larapacks/authorization)
[![License](https://img.shields.io/packagist/l/larapacks/authorization.svg?style=flat-square)](https://packagist.org/packages/larapacks/authorization)

An easy, native role / permission management system for Laravel.

## Index

- [Installation](#installation)
  - [Migration Customization](#migration-customization)
  - [Model Customization](#model-customization)
- [Usage](#usage)
- [Checking Permissions & Roles](#checking-permissions--roles)
- [Caching](#caching)
- [Gate Registration](#gate-registration)
- [Middleware](#middleware)
- [Testing](#running-tests)

## Installation

> **Note**: Laravel 5.5 or greater is required.

To get started, install Authorization via the Composer package manager:

    composer require larapacks/authorization

The Authorization service provider registers its own database migration directory
with the framework, so you should migrate your database after installing the
package. The Authorization migrations will create the tables your
application needs to store roles and permissions:

    php artisan migrate

Now insert the `Larapacks\Authorization\Traits\Authorizable` onto your `App\User` model:

```php
<?php

namespace App;

use Larapacks\Authorization\Traits\Authorizable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Authorizable;
```

You can now perform user authorization.

### Migration Customization

If you would not like to use Authorizations default migrations, you should call the
`Authorization::ignoreMigrations` method in the `register` method of your
`AppServiceProvider`. You may export the default migrations using
`php artisan vendor:publish --tag=authorization-migrations`.

```php
use Larapacks\Authorization\Authorization;

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

By default, the `App\User` class is registered as the authorizable user model.

You're free to extend the models used internally by Authorization, or create your own.

Instruct Authorization to use your own models via the `Authorization` class in your `AuthServiceProvider`:

```php
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Larapacks\Authorization\Authorization;

/**
 * Register any authentication / authorization services.
 *
 * @return void
 */
public function boot()
{
    $this->registerPolicies();

    Authorization::useUserModel(User::class);
    Authorization::useRoleModel(Role::class);
    Authorization::usePermissionModel(Permission::class);
}
```

Be sure to add the relevant traits for each of your custom models:

**Role Model**:

```php
use Larapacks\Authorization\Traits\ManagesPermissions;

class Role extends Model
{
    use ManagesPermissions;
```

**Permission Model**:

```php
use Larapacks\Authorization\Traits\HasUsers;
use Larapacks\Authorization\Traits\HasRoles;
use Larapacks\Authorization\Traits\ClearsCachedPermissions;

class Permission extends Model
{
    use HasUsers, HasRoles, ClearsCachedPermissions;
```

## Usage

Authorization utilizes native Laravel relationships, so there's no need to learn a new API.

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

$administrator->permissions()->save($createUsers);
```

Now assign the role to the user:

```php
$user->roles()->save($administrator);
```

You can also create user specific permissions:

```php
$createUsers = new Permission();

$createUsers->name = 'users.create';
$createUsers->label = 'Create Users';

$createUsers->save();

$user->permissions()->save($createUsers);
```

### Checking Permissions & Roles

Using Laravel's native `$user->can()` method:

```php
if ($user->can('users.create')) {
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

### Caching

By default all permissions are cached to prevent them from being retrieved on every request.

This cache is automatically flushed when permissions are created, updated, or deleted.

If you would like to disable the cache, call `Authorization::disablePermissionCache` in your `AuthServiceProvider`:

```php
use Larapacks\Authorization\Authorization;

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
use Larapacks\Authorization\Authorization;

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
use Larapacks\Authorization\Authorization;

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
use Larapacks\Authorization\Authorization;

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
    'role' => \Larapacks\Authorization\Middleware\RoleMiddleware::class,

    // The permission middleware:
    'permission' => \Larapacks\Authorization\Middleware\PermissionMiddleware::class,
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
use Larapacks\Authorization\PermissionRegistrar;
```

```php
protected function setUp() : void
{
    parent::setUp();

    app(PermissionResistrar::class)->register();
}
```

## Upgrading v1 to v2

### Configuration

Configuration is now done via static methods on the `Authorization` class.

You may delete the published `config/authorization.php` file.

### Traits

The `UserRolesTrait` has been renamed to `Authorizable`.

The `PermissionRolesTrait` has been separated into multiple traits.
You must apply the `HasRoles`, `HasUsers`, and `ClearsCachedPermissions` traits.

The `RolePermissionsTrait` has been renamed to `ManagesPermissions`.

### Closure Permissions

Permission closures have been removed. If you still require this functionality, continue using v1.
