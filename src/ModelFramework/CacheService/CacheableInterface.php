<?php
namespace ModelFramework\CacheService;

interface CacheableInterface
{
    public function set($key, $data);

    public function get($key);

    public function delete($key);

    public function exists($key);

    public function clear();
}
