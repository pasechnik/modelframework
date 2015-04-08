<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/29/14
 * Time: 6:29 PM
 */

namespace ModelFramework\ViewService\ViewConfig;

use ModelFramework\DataModel\DataModelInterface;

interface ViewConfigAwareInterface
{
    /**
     * @param ViewConfig|DataModelInterface $viewConfig
     *
     * @return $this
     */
    public function setViewConfig(ViewConfig $viewConfig);

    /**
     * @return ViewConfig|DataModelInterface
     */
    public function getViewConfig();

    /**
     * @return ViewConfig|DataModelInterface
     * @throws \Exception
     */
    public function getViewConfigVerify();
}
