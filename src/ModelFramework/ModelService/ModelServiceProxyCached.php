<?php

namespace ModelFramework\ModelService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;
use ModelFramework\DataModel\DataModel;

/**
 * Class ModelService
 * @package ModelFramework\ModelService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class ModelServiceProxyCached implements ModelServiceAwareInterface, CacheServiceAwareInterface, ModelServiceInterface
{
    use CacheServiceAwareTrait, ModelServiceAwareTrait;

    /**
     * @param string $modelName
     *
     * @return DataModel
     */
    public function get($modelName)
    {
        return $this->getModel($modelName);
    }

    /**
     * @param string $modelName
     *
     * @return DataModel
     */
    public function getModel($modelName)
    {
        return $this->getCacheService()->getCachedObjMethod($this->getModelService(), 'getModel', [ $modelName ]);
    }
}
