<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/29/14
 * Time: 6:29 PM
 */

namespace ModelFramework\ViewBoxService\ViewBoxConfig;

interface ViewBoxConfigAwareInterface
{
    /**
     * @param ViewBoxConfig $viewBoxConfig
     *
     * @return $this
     */
    public function setViewBoxConfig(ViewBoxConfig $viewBoxConfig);

    /**
     * @return ViewBoxConfig
     */
    public function getViewBoxConfig();

    /**
     * @return ViewBoxConfig
     * @throws \Exception
     */
    public function getViewBoxConfigVerify();
}
