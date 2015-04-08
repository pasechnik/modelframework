<?php
/**
 * Class LogicServiceProxyCached
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use Zend\Mvc\Controller\Plugin\Params;

class LogicServiceProxyCached
    implements LogicServiceInterface, LogicServiceAwareInterface, CacheServiceAwareInterface, ParamsAwareInterface
{
    use LogicServiceAwareTrait, CacheServiceAwareTrait;

    /**
     * @param string $eventName
     * @param string $modelName
     *
     * @return Logic
     */
    public function get($eventName, $modelName)
    {
        return $this->getCacheService()
                    ->getCachedObjMethod($this->getLogicServiceVerify(), 'get', [ $eventName, $modelName ]);
    }

    /**
     * @param string $eventName
     * @param string $modelName
     *
     * @return Logic
     */
    public function createLogic($eventName, $modelName)
    {
        return $this->getCacheService()
                    ->getCachedObjMethod($this->getLogicServiceVerify(), 'createLogic', [ $eventName, $modelName ]);
    }

    /**
     * @param $event
     *
     * @return mixed
     */
    public function dispatch($event)
    {
        return $this->getLogicServiceVerify()->dispatch($event);
    }

    /**
     * @param Params $params
     *
     * @return $this
     */
    public function setParams(Params $params)
    {
        return $this->getLogicServiceVerify()->setParams($params);
    }

    /**
     * @return Params
     */
    public function getParams()
    {
        return $this->getLogicServiceVerify()->getParams();
    }

    /**
     * @return Params
     * @throws \Exception
     */
    public function getParamsVerify()
    {
        return $this->getLogicServiceVerify()->getParamsVerify();
    }
}
