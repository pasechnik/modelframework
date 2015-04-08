<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:42 PM
 */

namespace ModelFramework\ViewService\ViewConfig;

use ModelFramework\DataModel\DataModelInterface;

trait ViewConfigAwareTrait
{
    /**
     * @var ViewConfig|DataModelInterface
     */
    private $_viewConfig = null;

    /**
     * @param ViewConfig|DataModelInterface $viewConfig
     *
     * @return $this
     */
    public function setViewConfig(ViewConfig $viewConfig)
    {
        $this->_viewConfig = $viewConfig;
    }

    /**
     * @return ViewConfig|DataModelInterface
     *
     */
    public function getViewConfig()
    {
        return $this->_viewConfig;
    }

    /**
     * @return ViewConfig|DataModelInterface
     * @throws \Exception
     */
    public function getViewConfigVerify()
    {
        $viewConfig = $this->getViewConfig();
        if ($viewConfig == null || ! $viewConfig instanceof ViewConfig) {
            throw new \Exception('ViewConfig does not set in ViewConfigAware instance of '.
                                  get_class($this));
        }

        return $viewConfig;
    }
}
