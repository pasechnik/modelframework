<?php
/**
 * Class GatewayServiceProxyCached
 * @package ModelFramework\GatewayService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\GatewayService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;

class GatewayServiceProxyCached
    implements GatewayServiceAwareInterface, CacheServiceAwareInterface, GatewayServiceInterface
{
    use GatewayServiceAwareTrait, CacheServiceAwareTrait, ModelServiceAwareTrait;

    /**
     * @param string             $name
     * @param DataModelInterface $model
     *
     * @return null|MongoGateway
     */
    public function get($name, DataModelInterface $model = null)
    {
        return $this->getGateway($name, $model);
    }

    /**
     * @param string             $name
     * @param DataModelInterface $model
     *
     * @return null|MongoGateway
     * @throws \Exception
     */
    public function getGateway($name, DataModelInterface $model = null)
    {
        return $this->getCacheServiceVerify()
                    ->getCachedObjMethod($this->getGatewayServiceVerify(), 'getGateway', [ $name, $model ]);
    }
}
