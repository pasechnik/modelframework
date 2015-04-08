<?php
/**
 * Class LogicConfigAwareTrait
 * @package ModelFramework\DataModel\Custom
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\LogicConfig;

use ModelFramework\DataModel\DataModelInterface;

trait LogicConfigAwareTrait
{
    /**
     * @var LogicConfig|DataModelInterface
     */
    private $_logicConfig = null;

    /**
     * @param LogicConfig|DataModelInterface $logicConfig
     *
     * @return $this
     */
    public function setLogicConfig(LogicConfig $logicConfig)
    {
        $this->_logicConfig = $logicConfig;
    }

    /**
     * @return LogicConfig|DataModelInterface
     *
     */
    public function getLogicConfig()
    {
        return $this->_logicConfig;
    }

    /**
     * @return LogicConfig|DataModelInterface
     * @throws \Exception
     */
    public function getLogicConfigVerify()
    {
        $logicConfig = $this->getLogicConfig();
        if ($logicConfig == null || !$logicConfig instanceof LogicConfig) {
            throw new \Exception('Logic Config Data does not set set in DataLogic');
        }

        return $logicConfig;
    }
}
