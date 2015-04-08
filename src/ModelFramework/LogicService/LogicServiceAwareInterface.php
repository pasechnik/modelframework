<?php
/**
 * Class LogicServiceAwareInterface
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

interface LogicServiceAwareInterface
{
    /**
     * @param LogicServiceInterface $logicService
     *
     * @return mixed
     */
    public function setLogicService(LogicServiceInterface $logicService);

    /**
     * @return LogicServiceInterface
     */
    public function getLogicService();

    /**
     * @return LogicServiceInterface
     * @throws \Exception
     */
    public function getLogicServiceVerify();
}
