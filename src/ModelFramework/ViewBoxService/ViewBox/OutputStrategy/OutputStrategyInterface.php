<?php
namespace ModelFramework\ViewBoxService\ViewBox\OutputStrategy;

use ModelFramework\ViewBoxService\ViewBox\ViewBoxAwareInterface;

/**
 * Strategy for output PDF file
 * Interface OutputStrategyInterface
 * @package ModelFramework\ViewBoxService\Output\Strategy
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
interface OutputStrategyInterface extends ViewBoxAwareInterface
{

    /**
     * Generate output data
     * @return mixed
     */
    public function output();

}
