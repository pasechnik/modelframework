<?php

namespace ModelFramework\ViewBoxService;

interface ViewBoxServiceAwareInterface
{
    /**
     * @param ViewBoxServiceInterface $modelViewService
     *
     * @return $this
     */
    public function setViewBoxService(ViewBoxServiceInterface $modelViewService);

    /**
     * @return ViewBoxServiceInterface
     */
    public function getViewBoxService();

    /**
     * @return ViewBoxServiceInterface
     * @throws \Exception
     */
    public function getViewBoxServiceVerify();
}
