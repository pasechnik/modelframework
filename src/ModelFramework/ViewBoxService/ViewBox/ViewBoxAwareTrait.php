<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 10.02.15
 * Time: 10:05
 */

namespace ModelFramework\ViewBoxService\ViewBox;


trait ViewBoxAwareTrait {
    /**
     * @var ViewBoxInterface
     */
    private $_viewBox = null;

    /**
     * @param ViewBoxInterface $viewBox
     *
     * @return $this
     */
    public function setViewBox(ViewBoxInterface $viewBox)
    {
        $this->_viewBox = $viewBox;
    }

    /**
     * @return ViewBoxInterface
     */
    public function getViewBox()
    {
        return $this->_viewBox;
    }

    /**
     * @return ViewBoxInterface
     * @throws \Exception
     */
    public function getViewBoxVerify()
    {
        $_viewBox =  $this->getViewBox();
        if ($_viewBox == null || ! $_viewBox instanceof ViewBoxInterface) {
            throw new \Exception('ViewBox does not set in the ViewBoxAware instance of '.
                get_class($this));
        }

        return $_viewBox;
    }
}
