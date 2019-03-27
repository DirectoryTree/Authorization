<?php

namespace Larapacks\Authorization\Tests;

use Illuminate\Support\Collection;
use Larapacks\Authorization\Authorization;
use Larapacks\Authorization\PermissionRegistrar;
use Larapacks\Authorization\Tests\Stubs\User;

class AuthorizationTest extends TestCase
{
    /**
     * Returns a new stub user instance.
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
     * @return \Larapacks\Authorization\Role
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
     * @return \Larapacks\Authorization\Permission
     */
    protected function createPermission($attributes = [])
    {
        $permission = Authorization::permission();

        return $permission::create($attributes);
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

        $this->assertInstanceOf(Authorization::permissionModel(), $admin->grant($createUsers));
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

        $this->assertInstanceOf(Authorization::permissionModel(), $admin->revoke($createUsers));
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

        $this->assertInstanceOf(Authorization::permissionModel(), $admin->grant($editUser));
        $this->assertInstanceOf(Authorization::permissionModel(), $admin->grant($editUser));
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

        $this->assertInstanceOf(Authorization::permissionModel(), $admin->revoke($editUser));
        $this->assertInstanceOf(Authorization::permissionModel(), $admin->revoke($editUser));
    }

    public function test_permissions_are_cached_in_registrar()
    {
        $permissions = collect([
            $this->createPermission([
                'name'  => 'create',
                'label' => 'Create',
            ]),
            $this->createPermission([
                'name'  => 'edit',
                'label' => 'Edit',
            ])
        ]);

        $keys = $permissions->map->id;

        $this->assertEquals($keys, app(PermissionRegistrar::class)->getPermissions()->map->getKey());
        $this->assertEquals($keys, cache(Authorization::cacheKey())->map->getKey());
    }

    public function test_permission_cache_is_flushed_when_permissions_are_created()
    {
        $p = $this->createPermission([
            'name'  => 'create',
            'label' => 'Create',
        ]);

        $this->assertEquals($p->getKey(), app(PermissionRegistrar::class)->getPermissions()->first()->getKey());
        $this->assertEquals($p->getKey(), cache(Authorization::cacheKey())->first()->getKey());

        $this->createPermission([
            'name'  => 'edit',
            'label' => 'Edit',
        ]);

        $this->assertNull(cache(Authorization::cacheKey()));
    }

    public function test_permission_cache_is_flushed_when_permissions_are_updated()
    {
        $p = $this->createPermission([
            'name'  => 'create',
            'label' => 'Create',
        ]);

        $this->assertEquals($p->getKey(), app(PermissionRegistrar::class)->getPermissions()->first()->getKey());

        $p->update(['name' => 'edit', 'label' => 'Edit']);

        $this->assertNull(cache(Authorization::cacheKey()));
    }

    public function test_permission_cache_is_flushed_when_permissions_are_deleted()
    {
        $p = $this->createPermission([
            'name'  => 'create',
            'label' => 'Create',
        ]);

        $this->assertEquals($p->getKey(), app(PermissionRegistrar::class)->getPermissions()->first()->getKey());

        $p->delete();

        $this->assertNull(cache(Authorization::cacheKey()));
    }

    public function test_permission_cache_is_flushed_when_permissions_are_added_to_role()
    {
        $p = $this->createPermission(['name' => 'create', 'label' => 'Create']);

        $r = $this->createRole(['name' => 'admin', 'label' => 'Administrator']);

        $this->assertEquals($p->getKey(), app(PermissionRegistrar::class)->getPermissions()->first()->getKey());
        $this->assertEquals($p->getKey(), cache(Authorization::cacheKey())->first()->getKey());

        $r->permissions()->save($p);

        $this->assertNull(cache(Authorization::cacheKey()));
    }

    public function test_user_does_not_have_permission_with_cached_permissions()
    {
        $u = $this->createUser(['name' => 'John Doe']);

        $p = $this->createPermission(['name' => 'create', 'label' => 'Create']);

        $r = $this->createRole(['name' => 'admin', 'label' => 'Administrator']);

        // Attach the permission to the role.
        $r->permissions()->attach($p);

        // Attach the role to the user.
        $u->roles()->attach($r);

        // User has permission.
        $this->assertTrue($u->hasPermission($p));
        $this->assertTrue($u->hasPermission('create'));

        $this->assertEquals($p->getKey(), app(PermissionRegistrar::class)->getPermissions()->first()->getKey());
        $this->assertEquals($p->getKey(), cache(Authorization::cacheKey())->first()->getKey());

        // Detach the permission from the role.
        $r->permissions()->detach($p);

        // User no longer has permission.
        $this->assertFalse($u->hasPermission($p));
        $this->assertFalse($u->hasPermission('create'));
    }

    public function test_permissions_are_not_cached_when_disabled()
    {
        Authorization::disablePermissionCache();

        $p = $this->createPermission(['name' => 'create', 'label' => 'Create']);

        $this->assertEquals($p->getKey(), app(PermissionRegistrar::class)->getPermissions()->first()->getKey());
        $this->assertNull(cache(Authorization::cacheKey()));
    }
}
