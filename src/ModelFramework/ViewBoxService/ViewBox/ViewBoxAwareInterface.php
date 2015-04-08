<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 10.02.15
 * Time: 10:00
 */

namespace ModelFramework\ViewBoxService\ViewBox;


interface ViewBoxAwareInterface {

    /**
     * @param ViewBoxInterface $viewBox
     *
     * @return $this
     */
    public function setViewBox(ViewBoxInterface $viewBox);

    /**
     * @return ViewBoxInterface
     */
    public function getViewBox();

    /**
     * @return ViewBoxInterface
     * @throws \Exception
     */
    public function getViewBoxVerify();

}
