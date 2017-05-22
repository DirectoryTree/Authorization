<?php

namespace Larapacks\Authorization\Tests;

use Illuminate\Support\Facades\Schema;
use Larapacks\Authorization\AuthorizationServiceProvider;
use Larapacks\Authorization\Tests\Stubs\Permission;
use Larapacks\Authorization\Tests\Stubs\Role;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        // Create the users table for testing
        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
        });

        $this->loadMigrationsFrom(realpath(__DIR__.'/../src/Migrations'));

        $this->artisan('migrate');
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
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('authorization.role', Role::class);
        $app['config']->set('authorization.permission', Permission::class);
    }
}
