<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/24/14
 * Time: 8:20 PM
 */

namespace ModelFramework\AclService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;

class AclServiceProxyCached
    implements AclServiceInterface,
               AclServiceAwareInterface,
               CacheServiceAwareInterface
{

    use AclServiceAwareTrait, CacheServiceAwareTrait;

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function get($modelName)
    {
        return $this->getAclDataModel($modelName);
    }

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclConfig($modelName)
    {
        return $this->getCacheServiceVerify()
            ->getCachedObjMethod($this->getAclServiceVerify(), 'getAclConfig',
                [$modelName]);
    }
    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getVisibleFields($modelName)
    {
        return $this->getCacheServiceVerify()
                    ->getCachedObjMethod($this->getAclServiceVerify(), 'getVisibleFields',
                        [$modelName]);
    }

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclDataModel($modelName)
    {
        return $this->getCacheServiceVerify()
            ->getCachedObjMethod($this->getAclServiceVerify(),
                'getAclDataModel', [$modelName]);
    }
}
