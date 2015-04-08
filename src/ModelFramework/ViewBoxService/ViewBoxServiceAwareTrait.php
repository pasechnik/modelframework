<?php

namespace ModelFramework\ViewBoxService;

trait ViewBoxServiceAwareTrait
{
    /**
     * @var ViewBoxServiceInterface
     */
    private $_viewBoxService = null;

    /**
     * @param ViewBoxServiceInterface $viewBoxService
     *
     * @return $this
     */
    public function setViewBoxService(ViewBoxServiceInterface $viewBoxService)
    {
        $this->_viewBoxService = $viewBoxService;
    }

    /**
     * @return ViewBoxServiceInterface
     */
    public function getViewBoxService()
    {
        return $this->_viewBoxService;
    }

    /**
     * @return ViewBoxServiceInterface
     * @throws \Exception
     */
    public function getViewBoxServiceVerify()
    {
        $_viewBoxService =  $this->getViewBoxService();
        if ($_viewBoxService == null || ! $_viewBoxService instanceof ViewBoxServiceInterface) {
            throw new \Exception('ViewBoxService does not set in the ViewBoxServiceAware instance of '.
                                  get_class($this));
        }

        return $_viewBoxService;
    }
}
