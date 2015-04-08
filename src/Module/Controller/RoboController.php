<?php

namespace Wepo\Controller;

use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\LogicService\LogicServiceAwareInterface;
use ModelFramework\LogicService\LogicServiceAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use Wepo\Model\Status;
use Zend\Console\Request as ConsoleRequest;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\ControllerManager;

class RoboController extends AbstractActionController
    implements GatewayServiceAwareInterface, AuthServiceAwareInterface,
               LogicServiceAwareInterface, QueryServiceAwareInterface

{

    use GatewayServiceAwareTrait, AuthServiceAwareTrait, LogicServiceAwareTrait, QueryServiceAwareTrait;


    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        parent::setServiceLocator($serviceLocator);
        if ( !$serviceLocator instanceof ControllerManager
        ) {
            $this->setGatewayService($serviceLocator->get('ModelFramework\GatewayService'));
            $this->setAuthService($serviceLocator->get('ModelFramework\AuthService'));
            $this->setLogicService($serviceLocator->get('ModelFramework\LogicService'));
            $this->setQueryService($serviceLocator->get('ModelFramework\QueryService'));
        }
    }

    public function consoleAction()
    {
        $request = $this->getServiceLocator()->get('request');
        if ($request instanceof ConsoleRequest) {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '-1');

            $this->checkPermission();

            $purpose   = $this->params('purpose', null);
            $model     = $this->params('model', null);
            $logicType = $this->params('logic_type', 'zohoimport');

            switch ($purpose) {
                case 'logic':
                    prn('started logic');
                    $this->triggerLogic($logicType, $model);
                    prn('end logic');
                    break;
            }
        }
    }

    private function checkPermission()
    {
        $gw = $this->getGatewayServiceVerify()->get('MainUser');

        $login = $this->params('login', null);

        $check = $gw->find(
            [
                'login'     => $login,
                'status_id' => [Status::NEW_, Status::NORMAL]
            ]);

        if ($check->count()) {
            $mainUser = $check->current();

            $this->getAuthServiceVerify()->setMainUser($mainUser);
            $this->getLogicServiceVerify()
                ->get('signin', $mainUser->getModelName())
                ->trigger($mainUser);
        }

        return true;
    }

    public function triggerLogic($logicType, $modelName)
    {
        $count = 0;
        $listQuery
               = $this->getQueryServiceVerify()->get($modelName . '.robot')
            ->setParams($this->params())->process();
        $where = $listQuery->getWhere();
        $models
               = $this->getGatewayServiceVerify()->get($modelName)
            ->find($where);
        foreach ($models as $model) {
            prn($modelName . '.' . $logicType, ++$count,
                (string)$model->title);
            $this->getLogicServiceVerify()->get($logicType, $modelName)
                ->trigger($model);
        }
    }

}
