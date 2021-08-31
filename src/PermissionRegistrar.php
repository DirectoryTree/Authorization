<?php

namespace DirectoryTree\Authorization;

use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Auth\Access\Gate;
use PDOException;

class PermissionRegistrar
{
    /**
     * The auth gate.
     *
     * @var Gate
     */
    protected $gate;

    /**
     * The cache manager.
     *
     * @var CacheManager
     */
    protected $cache;

    /**
     * PermissionRegistrar constructor.
     *
     * @param Gate         $gate
     * @param CacheManager $manager
     */
    public function __construct(Gate $gate, CacheManager $manager)
    {
        $this->gate = $gate;
        $this->cache = $manager;
    }

    /**
     * Registers permissions into the gate.
     *
     * @return void
     */
    public function register()
    {
        // Dynamically register permissions with Laravel's Gate.
        foreach ($this->getPermissions() as $permission) {
            $this->gate->define($permission->name, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }

    /**
     * Fetch the collection of permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function getPermissions()
    {
        try {
            if (Authorization::$cachesPermissions) {
                return $this->cache->remember(Authorization::cacheKey(), Authorization::cacheExpiresIn(), function () {
                    return Authorization::permission()->get();
                });
            }

            return Authorization::permission()->get();
        } catch (PDOException $e) {
            // Migrations haven't been ran yet.
        }

        return [];
    }

    /**
     * Flushes the permission cache.
     *
     * @return void
     */
    public function flushCache()
    {
        $this->cache->forget(Authorization::cacheKey());
    }
}
