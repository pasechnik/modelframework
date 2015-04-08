<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/29/14
 * Time: 7:03 PM
 */

namespace ModelFramework\ViewService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;

class ViewServiceProxyCached
    implements ViewServiceInterface, CacheServiceAwareInterface, ViewServiceAwareInterface
{
    use CacheServiceAwareTrait, ViewServiceAwareTrait;

    /**
     * @param string $viewName
     *
     * @return View|ViewInterface
     * @throws \Exception
     */
    public function getView($viewName)
    {
        return $this->getCacheServiceVerify()
                    ->getCachedObjMethod($this->getViewServiceVerify(), 'getView', [ $viewName ]);
    }

    /**
     * @param string $viewName
     *
     * @return View|ViewInterface
     * @throws \Exception
     */
    public function get($viewName)
    {
        return $this->getView($viewName);
    }
}
