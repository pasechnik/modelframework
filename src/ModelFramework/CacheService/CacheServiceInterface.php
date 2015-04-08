<?php
namespace ModelFramework\CacheService;

interface CacheServiceInterface extends CacheableInterface
{
    public function getCachedObjMethod($obj, $method, $params);
}
