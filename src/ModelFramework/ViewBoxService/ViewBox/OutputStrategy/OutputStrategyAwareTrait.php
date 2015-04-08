<?php
namespace ModelFramework\ViewBoxService\ViewBox\OutputStrategy;

/**
 * Class OutputStrategyAwareTrait
 *
 * @package ModelFramework\ViewBoxService\Output\Strategy
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
trait OutputStrategyAwareTrait
{
    /**
     * @var null
     */
    private $_strategy = null;

    /**
     * Set Strategy
     *
     * @param OutputStrategyInterface $strategy
     *
     * @return $this
     */
    protected function setStrategy(OutputStrategyInterface $strategy)
    {
        $this->_strategy = $strategy;
        return $this;
    }

    /**
     * Get Strategy
     *
     * @return OutputStrategyInterface
     */
    protected function getStrategy()
    {
        return $this->_strategy;
    }

    /**
     * @return OutputStrategyInterface
     * @throws \Exception
     */
    public function getStrategyVerify()
    {
        $_strategy =  $this->getStrategy();
        if ($_strategy == null || ! $_strategy instanceof OutputStrategyInterface) {
            throw new \Exception('OutputStrategy does not set in the OutputStrategyAware instance of '.
                get_class($this));
        }

        return $_strategy;
    }

    /**
     * choose Strategy
     *
     * @param string $type
     *
     * @return $this
     */
    public function chooseStrategy($type)
    {
        switch ($type) {
            case 'pdf':
                $this->setStrategy(new PDFOutputStrategy());
                break;
            case 'html':
                $this->setStrategy(new HtmlOutputStrategy());
                break;
            default:
                $this->setStrategy(new HtmlOutputStrategy());
        }
        return $this;
    }
}
