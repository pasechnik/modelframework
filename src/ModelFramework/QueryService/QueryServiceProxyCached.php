<?php
/**
 * Class QueryServiceProxyCached
 *
 * @package ModelFramework\QueryService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;

class QueryServiceProxyCached
    implements QueryServiceInterface, CacheServiceAwareInterface,
               QueryServiceAwareInterface
{

    use CacheServiceAwareTrait, QueryServiceAwareTrait;

    /**
     * @param string $key
     *
     * @return Query|QueryInterface
     * @throws \Exception
     */
    public function getQuery($key)
    {
        return $this->getCacheServiceVerify()
            ->getCachedObjMethod($this->getQueryServiceVerify(), 'getQuery',
                [$key]);
    }

    /**
     * @param string $key
     *
     * @return Query|QueryInterface
     * @throws \Exception
     */
    public function get($key)
    {
        return $this->getQuery($key);
    }

    public function setParams($params)
    {
        $this->getQueryServiceVerify()->setParams($params);
    }
}
