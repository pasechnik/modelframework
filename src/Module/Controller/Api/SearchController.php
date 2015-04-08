<?php

namespace Wepo\Controller\Api;

use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\ControllerManager;
use Zend\View\Model\JsonModel;

class SearchController extends AbstractActionController
    implements ParamsAwareInterface, QueryServiceAwareInterface,
               GatewayServiceAwareInterface
{

    use ParamsAwareTrait, QueryServiceAwareTrait, GatewayServiceAwareTrait;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        parent::setServiceLocator($serviceLocator);

        if ( !$serviceLocator instanceof ControllerManager) {
            $this->setParams($this->params());
            $query = $serviceLocator->get('ModelFramework\QueryService');
            $query->setParams($this->params());
            $this->setQueryService($query);
            $this->setGatewayService($serviceLocator->get('ModelFramework\GatewayService'));
        }
    }

    public function indexAction()
    {
        $scopes = [
            'User',
            'Lead',
            'Patient',
        ];

        $scope = $this->getParam('scope', 'all');
        if (in_array($scope, ['email', 'lead', 'patient', 'user', 'product'])) {
            $scopes = ucfirst($scope);
        }
        if ($scope == 'pricebookdetail') {
            $scopes = ['PricebookDetail'];
        }

        $maxItems = 5;
        if (count($scopes) == 1) {
            $maxItems = 0;
        }

        foreach ($scopes as $model) {

            $query = $this->getQueryServiceVerify()->get($model . '.list')
                ->process();

            $results = $this->getGatewayServiceVerify()->get($model)
                ->find($query->getWhere(), $query->getOrder(), $maxItems);

//            $errors['res'][] = $results->toArray();
            foreach ($results as $result) {
                $data[] = ['_id' => $result->id(), 'title' => $result->title];
            }
        }

        return new JsonModel([
            'q'    => $this->getParam('q', ''),
            'data' => $data,
        ]);
    }

}
