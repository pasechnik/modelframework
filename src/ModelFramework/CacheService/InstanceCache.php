<?php

namespace ModelFramework\CacheService;

class InstanceCache implements CacheableInterface
{
    protected $_store = [ ];

    /**
     * Save data to the cache
     */
    public function set($key, $data)
    {
        $this->_store[ $key ] = $data;

        return $this;
    }

    /**
     * Get the specified data from the cache
     */
    public function get($key)
    {
        if ($this->exists($key)) {
            $data = $this->_store[ $key ];

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
            unset($this->_store[ $key ]);
        }

        return $this;
    }

    /**
     * Check if the specified cache key exists
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->_store);
    }

    /**
     * Clear the cache
     */
    public function clear()
    {
        unset($this->_store);
        $this->_store = [ ];

        return true;
    }
}
