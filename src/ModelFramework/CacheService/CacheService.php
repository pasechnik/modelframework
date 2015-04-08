<?php

namespace ModelFramework\CacheService;

use ModelFramework\BaseService\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class CacheService implements CacheServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    /**
     * @var CacheableInterface
     */
    protected $_cache = null;
    protected $_user = '';
    protected $_company = '';

    public function init(CacheableInterface $cache = null)
    {
        if ($this->_cache == null) {
            if ($cache == null) {
                $this->_cache = new InstanceCache();
            } else {
                $this->_cache = $cache;
            }
        }

//        $auth = $this->getServiceLocator()->get('ModelFramework\AuthService');
//        $this->_user = $auth->getMainUser()->id();
//        $this->_company = (string)$auth->getMainUser()-> company_id;
    }

    public function getCachedObjMethod($obj, $method, $params)
    {
        $this->init();

        $a = [
            'object' => get_class($obj), 'method' => $method, 'user' => $this->_user,
//            , 'company' => $this->_company
        ];
        $a += $params;
        $cacheKey = md5(serialize($a));

        if ($this->exists($cacheKey)) {
            $_temp  = $this->get($cacheKey);
            $result = is_object($_temp) ? clone $_temp : $_temp;
        } else {
            $reflectionMethod = new \ReflectionMethod($obj, $method);
            $result           = $reflectionMethod->invokeArgs($obj, $params);
            $this->set($cacheKey, $result);
        }

        return $result;
    }

    public function setUser($userId)
    {
        $this->_user = $userId;
    }

    public function setCompany($companyId)
    {
        $this->_company = $companyId;
    }

    public function setCache(CacheableInterface $cache)
    {
        $this->_cache = $cache;
    }

    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * Add the specified data to the cache
     */
    public function set($key, $data)
    {
        return $this->getCache()->set($key, $data);
    }

    /**
     * Get the specified data from the cache
     */
    public function get($key)
    {
        return $this->getCache()->get($key);
    }

    /**
     * Delete the specified data from the cache
     */
    public function delete($key)
    {
        $this->getCache()->delete($key);
    }

    /**
     * Check if the specified cache key exists
     */
    public function exists($key)
    {
        return $this->getCache()->exists($key);
    }

    /**
     * Clear the cache
     */
    public function clear()
    {
        return $this->getCache()->clear();
    }
}
