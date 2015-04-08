<?php

namespace ModelFramework\ViewService;

trait ViewServiceAwareTrait
{
    /**
     * @var ViewServiceInterface
     */
    private $_viewService = null;

    /**
     * @param ViewServiceInterface $viewService
     *
     * @return $this
     */
    public function setViewService(ViewServiceInterface $viewService)
    {
        $this->_viewService = $viewService;
    }

    /**
     * @return ViewServiceInterface
     */
    public function getViewService()
    {
        return $this->_viewService;
    }

    /**
     * @return ViewServiceInterface
     * @throws \Exception
     */
    public function getViewServiceVerify()
    {
        $_viewService =  $this->getViewService();
        if ($_viewService == null || ! $_viewService instanceof ViewServiceInterface) {
            throw new \Exception('ViewService does not set in the ViewServiceAware instance of '.
                                  get_class($this));
        }

        return $_viewService;
    }
}
