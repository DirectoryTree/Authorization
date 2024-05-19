<?php

namespace DirectoryTree\Authorization\Tests;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\WithFaker;
use DirectoryTree\Authorization\Authorization;
use DirectoryTree\Authorization\Tests\Stubs\User;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DirectoryTree\Authorization\AuthorizationServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(base_path('migrations'));
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            AuthorizationServiceProvider::class,
        ];
    }

    /**
     * Returns a new stub user instance.
     *
     * @param array $attributes
     *
     * @return User
     */
    protected function createUser($attributes = [])
    {
        $attributes['email'] = $this->faker->unique()->safeEmail;
        $attributes['password'] = Hash::make(Str::random(10));
        return User::create($attributes);
    }

    /**
     * Returns a new role instance.
     *
     * @param array $attributes
     *
     * @return \DirectoryTree\Authorization\Role
     */
    protected function createRole($attributes = [])
    {
        $role = Authorization::role();

        return $role::create($attributes);
    }

    /**
     * Returns a new permission instance.
     *
     * @param array $attributes
     *
     * @return \DirectoryTree\Authorization\Permission
     */
    protected function createPermission($attributes = [])
    {
        $permission = Authorization::permission();

        return $permission::create($attributes);
    }
}
