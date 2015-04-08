<?php
/**
 * Class LogicConfigAwareInterface
 * @package ModelFramework\DataModel\Custom
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\LogicConfig;

interface LogicConfigAwareInterface
{
    /**
     * @param LogicConfig $logicConfig
     *
     * @return $this
     */
    public function setLogicConfig(LogicConfig $logicConfig);

    /**
     * @return LogicConfig
     */
    public function getLogicConfig();

    /**
     * @return LogicConfig
     * @throws \Exception
     */
    public function getLogicConfigVerify();
}
