<?php

namespace Larapacks\Authorization\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Larapacks\Authorization\Authorization;
use Larapacks\Authorization\Tests\Stubs\Permission;
use Larapacks\Authorization\Tests\Stubs\Role;
use Larapacks\Authorization\Tests\Stubs\User;

class AuthorizationTest extends TestCase
{
    /**
     * Returns a new user instance.
     *
     * @param array $attributes
     *
     * @return User
     */
    protected function createUser($attributes = [])
    {
        return User::create($attributes);
    }

    /**
     * Returns a new role instance.
     *
     * @param array $attributes
     *
     * @return Role
     */
    protected function createRole($attributes = [])
    {
        return Role::create($attributes);
    }

    /**
     * Returns a new permission instance.
     *
     * @param array $attributes
     *
     * @return Permission
     */
    protected function createPermission($attributes = [])
    {
        return Permission::create($attributes);
    }

    public function test_assign_role()
    {
        $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $user->assignRole('administrator');

        $this->assertCount(1, $user->roles);
    }

    public function test_assign_multiple_roles()
    {
        $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $this->createRole([
            'name'  => 'member',
            'label' => 'Member',
        ]);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $user->assignRole('administrator');
        $user->assignRole('member');

        $this->assertCount(2, $user->roles);
    }

    public function test_assign_roles_with_model()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $member = $this->createRole([
            'name'  => 'member',
            'label' => 'Member',
        ]);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $user->assignRole($admin);
        $user->assignRole($member);

        $this->assertCount(2, $user->roles);
    }

    public function test_has_role()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $user->assignRole($admin);

        $this->assertTrue($user->hasRole($admin));
        $this->assertTrue($user->hasRole('administrator'));
        $this->assertFalse($user->hasRole('non-existent'));
    }

    public function test_grant_permission()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $user->assignRole($admin);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $this->assertInstanceOf(Permission::class, $admin->grant($createUsers));
        $this->assertTrue($user->hasPermission($createUsers));
    }

    public function test_grant_multiple_permissions()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $user->assignRole($admin);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $editUsers = $this->createPermission([
            'name'  => 'users.edit',
            'label' => 'Edit Users',
        ]);

        $granted = $admin->grant([$createUsers, $editUsers]);

        $this->assertInstanceOf(Collection::class, $granted);
        $this->assertCount(2, $granted);
        $this->assertTrue($user->hasPermission($createUsers));
        $this->assertTrue($user->hasPermission($editUsers));
    }

    public function test_grant_multiple_permissions_with_non_existent_permission()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $user->assignRole($admin);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $editUsers = $this->createPermission([
            'name'  => 'users.edit',
            'label' => 'Edit Users',
        ]);

        $this->assertEquals(2, $admin->grant([$createUsers, $editUsers, 'testing'])->count());
    }

    public function test_revoke_permission()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $user->assignRole($admin);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $admin->grant($createUsers);

        $this->assertInstanceOf(Permission::class, $admin->revoke($createUsers));
    }

    public function test_revoke_multiple_permissions()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $user->assignRole($admin);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $editUsers = $this->createPermission([
            'name'  => 'users.edit',
            'label' => 'Edit Users',
        ]);

        $admin->grant([$createUsers, $editUsers]);

        $this->assertEquals(2, $admin->revoke([$createUsers, $editUsers])->count());
    }

    public function test_revoke_multiple_permissions_with_non_existent_permission()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $user->assignRole($admin);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $editUsers = $this->createPermission([
            'name'  => 'users.edit',
            'label' => 'Edit Users',
        ]);

        $admin->grant([$createUsers, $editUsers]);

        $this->assertEquals(2, $admin->revoke([$createUsers, $editUsers, 'testing'])->count());
    }

    public function test_has_permission()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $permission = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $user->assignRole($admin);

        $admin->grant($permission);

        $this->assertTrue($user->hasPermission($permission));
        $this->assertTrue($user->hasPermission('users.create'));
        $this->assertFalse($user->hasPermission('non-existent'));
    }

    public function test_has_multiple_permissions()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $editUsers = $this->createPermission([
            'name'  => 'users.edit',
            'label' => 'Edit Users',
        ]);

        $admin->grant([$createUsers, $editUsers]);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $user->assignRole($admin);

        $this->assertTrue($user->hasPermissions([$createUsers, $editUsers]));
    }

    public function test_does_not_have_permission()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $admin->grant($createUsers);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $this->assertTrue($user->doesNotHavePermission($createUsers));
        $this->assertFalse($user->hasPermission($createUsers));
    }

    public function test_does_not_have_permission_multiple()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $editUsers = $this->createPermission([
            'name'  => 'users.edit',
            'label' => 'Edit Users',
        ]);

        $admin->grant($createUsers);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $user->assignRole($admin);

        $this->assertTrue($user->doesNotHavePermissions([$createUsers, $editUsers]));
    }

    public function test_has_any_permissions()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $editUsers = $this->createPermission([
            'name'  => 'users.edit',
            'label' => 'Edit Users',
        ]);

        $nonGrantedPermission = $this->createPermission([
            'name'  => 'other',
            'label' => 'Other Permission',
        ]);

        $admin->grant([$createUsers, $editUsers]);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $user->assignRole($admin);

        $this->assertTrue($user->hasAnyPermissions([$editUsers, $nonGrantedPermission]));
        $this->assertFalse($user->hasAnyPermissions([$nonGrantedPermission]));
    }

    public function test_role_has_permission()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $editUsers = $this->createPermission([
            'name'  => 'users.edit',
            'label' => 'Edit Users',
        ]);

        $admin->grant($createUsers);

        $this->assertTrue($admin->hasPermission('users.create'));
        $this->assertTrue($admin->hasPermission($createUsers));
        $this->assertFalse($admin->hasPermission('non-existent'));
        $this->assertFalse($admin->hasPermission($editUsers));
    }

    public function test_role_has_permissions()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $editUsers = $this->createPermission([
            'name'  => 'users.edit',
            'label' => 'Edit Users',
        ]);

        $deleteUsers = $this->createPermission([
            'name'  => 'users.destroy',
            'label' => 'Delete Users',
        ]);

        $admin->grant([$createUsers, $editUsers]);

        $this->assertTrue($admin->hasPermissions([$createUsers, $editUsers]));
        $this->assertFalse($admin->hasPermissions([$createUsers, $editUsers, $deleteUsers]));
    }

    public function test_role_has_any_permissions()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $createUsers = $this->createPermission([
            'name'  => 'users.create',
            'label' => 'Create Users',
        ]);

        $editUsers = $this->createPermission([
            'name'  => 'users.edit',
            'label' => 'Edit Users',
        ]);

        $deleteUsers = $this->createPermission([
            'name'  => 'users.destroy',
            'label' => 'Delete Users',
        ]);

        $admin->grant([$createUsers, $editUsers]);

        $this->assertTrue($admin->hasAnyPermissions([$createUsers, $editUsers, 'non-existent']));
        $this->assertFalse($admin->hasAnyPermissions(['non-existent', 'unknown', $deleteUsers]));
    }

    public function test_user_has_role()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $member = $this->createRole([
            'name'  => 'member',
            'label' => 'Member',
        ]);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $user->assignRole($admin);

        $this->assertTrue($user->hasRole('administrator'));
        $this->assertTrue($user->hasRole($admin));
        $this->assertFalse($user->hasRole('non-existent'));
        $this->assertFalse($user->hasRole($member));
    }

    public function test_user_has_roles()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $member = $this->createRole([
            'name'  => 'member',
            'label' => 'Member',
        ]);

        $guest = $this->createRole([
            'name'  => 'guest',
            'label' => 'Guest',
        ]);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $user->assignRole($admin);
        $user->assignRole($member);

        $this->assertTrue($user->hasRoles(['administrator', 'member']));
        $this->assertTrue($user->hasRoles([$admin, $member]));
        $this->assertFalse($user->hasRoles(['non-existent', $admin]));
        $this->assertFalse($user->hasRoles([$admin, $member, $guest]));
    }

    public function test_user_has_any_roles()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $member = $this->createRole([
            'name'  => 'member',
            'label' => 'Member',
        ]);

        $guest = $this->createRole([
            'name'  => 'guest',
            'label' => 'Guest',
        ]);

        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $user->assignRole($admin);
        $user->assignRole($member);

        $this->assertTrue($user->hasAnyRoles(['administrator', 'member', 'non-existent']));
        $this->assertTrue($user->hasAnyRoles([$admin, $member, 'non-existent']));
        $this->assertTrue($user->hasAnyRoles([$admin, $member, $guest]));
    }

    public function test_user_specific_permissions()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $editUser = $this->createPermission([
            'name'  => 'users.edit.1',
            'label' => 'Edit Specific User',
        ]);

        $user->permissions()->save($editUser);

        $this->assertTrue($user->hasPermission('users.edit.1'));
        $this->assertTrue($user->hasPermission($editUser));
    }

    public function test_granting_same_permissions()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $editUser = $this->createPermission([
            'name'  => 'users.edit.1',
            'label' => 'Edit Specific User',
        ]);

        $this->assertInstanceOf(Permission::class, $admin->grant($editUser));
        $this->assertInstanceOf(Permission::class, $admin->grant($editUser));
    }

    public function test_revoking_same_permissions()
    {
        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $editUser = $this->createPermission([
            'name'  => 'users.edit.1',
            'label' => 'Edit Specific User',
        ]);

        $admin->grant($editUser);

        $this->assertInstanceOf(Permission::class, $admin->revoke($editUser));
        $this->assertInstanceOf(Permission::class, $admin->revoke($editUser));
    }

    public function test_closure_permission()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $create = new Permission();

        $create->name = 'create-post';
        $create->label = 'Create Post';
        $create->closure = function ($user, $id) {
            return $user->id == $id;
        };

        $create->save();

        // Stub the service provider defined ability.
        Gate::define($create->name, $create->closure);

        $this->assertTrue($user->can('create-post', 1));
        $this->assertFalse($user->can('create-post', 2));
    }

    public function test_closure_permission_fails()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $create = new Permission();

        $create->name = 'create-post';
        $create->label = 'Create Post';
        $create->closure = function ($user, $id, $otherParameter) {
            return $user->id == $id;
        };

        $create->save();

        // Stub the service provider defined ability.
        Gate::define($create->name, $create->closure);

        $this->assertTrue($user->can('create-post', [1, 'other-parameter']));

        $this->setExpectedException(\ErrorException::class);

        // Missing argument three.
        $user->can('create-post', [1]);
    }

    public function test_user_helper()
    {
        config()->set('authorization.user', User::class);

        $this->assertInstanceOf(User::class, Authorization::user());
    }

    public function test_role_helper()
    {
        config()->set('authorization.role', Role::class);

        $this->assertInstanceOf(Role::class, Authorization::role());
    }

    public function test_permission_helper()
    {
        config()->set('authorization.permission', Permission::class);

        $this->assertInstanceOf(Permission::class, Authorization::permission());
    }

    public function test_user_is_administrator()
    {
        $user = $this->createUser([
            'name' => 'John Doe',
        ]);

        $userTwo = $this->createUser([
            'name' => 'John Doe',
        ]);

        $admin = $this->createRole([
            'name'  => 'administrator',
            'label' => 'Admin',
        ]);

        $user->assignRole($admin);

        $this->assertTrue($user->isAdministrator());
        $this->assertFalse($userTwo->isAdministrator());
    }

    public function test_create_role_command()
    {
        $this->artisan('create:role', [
            'name' => 'administrator',
        ]);

        $this->seeInDatabase('roles', [
            'name'  => 'administrator',
            'label' => 'Administrator',
        ]);
    }

    public function test_create_permission_command()
    {
        $this->artisan('create:permission', [
            'name' => 'manage-users',
        ]);

        $this->seeInDatabase('roles', [
            'name' => 'administrator',
            'label' => 'Administrator',
        ]);
    }
}
