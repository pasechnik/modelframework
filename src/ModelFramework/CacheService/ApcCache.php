<?php

namespace ModelFramework\CacheService;

class ApcCache implements CacheableInterface
{
    /**
     * Save data to the cache
     */
    public function set($key, $data)
    {
        if (!apc_store($key, $data)) {
            throw new ApcCacheException('Error saving data with the key '.$key.' to the cache.');
        }

        return $this;
    }

    /**
     * Get the specified data from the cache
     */
    public function get($key)
    {
        if ($this->exists($key)) {
            if (!$data = apc_fetch($key)) {
                throw new ApcCacheException('Error fetching data with the key '.$key.' from the cache.');
            }

            return $data;
        }

        return;
    }

    /**
     * Delete the specified data from the cache
     */
    public function delete($key)
    {
        if ($this->exists($key)) {
            if (!apc_delete($key)) {
                throw new ApcCacheException('Error deleting data with the key '.$key.' from the cache.');
            }
        }

        return $this;
    }

    /**
     * Check if the specified cache key exists
     */
    public function exists($key)
    {
        return apc_exists($key);
    }

    /**
     * Clear the cache
     */
    public function clear($cacheType = 'user')
    {
        return apc_clear_cache($cacheType);
    }
}
