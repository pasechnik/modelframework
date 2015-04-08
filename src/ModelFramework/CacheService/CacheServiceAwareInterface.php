<?php
namespace ModelFramework\CacheService;

interface CacheServiceAwareInterface
{
    /**
     * @param CacheServiceInterface $modelService
     *
     * @return $this
     */
    public function setCacheService(CacheServiceInterface $modelService);

    /**
     * @return CacheServiceInterface
     */
    public function getCacheService();

    /**
     * @return CacheServiceInterface
     * @throws \Exception
     */
    public function getCacheServiceVerify();
}
