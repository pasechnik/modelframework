<?php

namespace ModelFramework\CacheService;

trait CacheServiceAwareTrait
{
    /**
     * @var CacheServiceInterface
     */
    private $_cacheService = null;

    /**
     * @param CacheServiceInterface $cacheService
     *
     * @return $this
     */
    public function setCacheService(CacheServiceInterface $cacheService)
    {
        $this->_cacheService = $cacheService;

        return $this;
    }

    /**
     * @return CacheServiceInterface
     */
    public function getCacheService()
    {
        return $this->_cacheService;
    }

    /**
     * @return CacheServiceInterface
     * @throws \Exception
     */
    public function getCacheServiceVerify()
    {
        $_cacheService = $this->getCacheService();
        if ($_cacheService == null || !$_cacheService instanceof CacheServiceInterface) {
            throw new \Exception('CacheService does not set in the CacheServiceAware instance of '.get_class($this));
        }

        return $_cacheService;
    }
}
