<?php
/**
 * Class LogicServiceInterface
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

interface LogicServiceInterface
{
    /**
     * @param string $eventName
     * @param string $modelName
     *
     * @return DataLogic
     */
    public function get($eventName, $modelName);

    /**
     * @param string $eventName
     * @param string $modelName
     *
     * @return DataLogic
     */
    public function createLogic($eventName, $modelName);
}
