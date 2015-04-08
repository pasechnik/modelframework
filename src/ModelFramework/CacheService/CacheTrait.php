<?php

namespace ModelFramework\CacheService;

use ModelFramework\BaseService\ServiceLocatorAwareTrait;

trait CacheTrait
{
    use ServiceLocatorAwareTrait;

    protected $_cacheServiceName = 'ModelFramework\CacheService';
    protected $_cacheMethodPrefix = '_';

    /**
     * Magic method is used to cache results of the object methods calls
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        return $this->callCached($name, $arguments);
//        $cacheHandler = $this->getServiceLocator()->get( $this->_cacheServiceName );
//        $result       = $cacheHandler->getCachedObjMethod( $this, $this->_cacheMethodPrefix . $name, $arguments );
//
//        return $result;
    }

    public function callCached($name, array $arguments)
    {
        $cacheHandler = $this->getServiceLocator()->get($this->_cacheServiceName);
        $result       = $cacheHandler->getCachedObjMethod($this, $this->_cacheMethodPrefix.$name, $arguments);

        return $result;
    }

    /**
     * @param       $object
     * @param       $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function cacheObjectMethod($object, $name, array $arguments)
    {
        $cacheHandler = $this->getServiceLocator()->get($this->_cacheServiceName);
        $result       = $cacheHandler->getCachedObjMethod($object, $name, $arguments);

        return $result;
    }
}
