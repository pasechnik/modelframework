<?php

namespace ModelFramework\ViewService;

interface ViewServiceAwareInterface
{
    /**
     * @param ViewServiceInterface $viewService
     *
     * @return $this
     */
    public function setViewService(ViewServiceInterface $viewService);

    /**
     * @return ViewServiceInterface
     */
    public function getViewService();

    /**
     * @return ViewServiceInterface
     * @throws \Exception
     */
    public function getViewServiceVerify();
}
