<?php

namespace ModelFramework\ViewBoxService;

interface ViewBoxServiceInterface
{
    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function getViewBox($viewBoxName);

    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function get($viewBoxName);

    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function createViewBox($viewBoxName);
}
