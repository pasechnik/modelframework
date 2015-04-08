<?php

namespace ModelFramework\ViewBoxService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;

class ViewBoxServiceProxyCached
    implements ViewBoxServiceInterface, CacheServiceAwareInterface, ViewBoxServiceAwareInterface
{
    use CacheServiceAwareTrait, ViewBoxServiceAwareTrait;

    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function getViewBox($viewBoxName)
    {
        return $this->getCacheServiceVerify()
                    ->getCachedObjMethod($this->getViewBoxServiceVerify(), 'getViewBox', [ $viewBoxName ]);
    }

    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function get($viewBoxName)
    {
        return $this->getViewBox($viewBoxName);
    }

    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function createViewBox($viewBoxName)
    {
        return $this->getCacheServiceVerify()
                    ->getCachedObjMethod($this->getViewBoxServiceVerify(), 'createViewBox', [ $viewBoxName ]);
    }
}
