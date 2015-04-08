<?php

namespace Wepo\Controller\Api;

use ModelFramework\AclService\AclServiceAwareInterface;
use ModelFramework\AclService\AclServiceAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class QueryController extends AbstractRestfulController
    implements ParamsAwareInterface, QueryServiceAwareInterface,
               GatewayServiceAwareInterface, AclServiceAwareInterface
{

    use ParamsAwareTrait, QueryServiceAwareTrait, GatewayServiceAwareTrait, AclServiceAwareTrait;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        parent::setServiceLocator($serviceLocator);
        if ( !$serviceLocator instanceof ControllerManager) {
            $this->setAclService($serviceLocator->get('ModelFramework\AclService'));
            $this->setGatewayService($serviceLocator->get('ModelFramework\GatewayService'));

            $this->setParams($this->params());
            $query = $serviceLocator->get('ModelFramework\QueryService');
            $query->setParams($this->params());
            $this->setQueryService($query);
        }
    }

//    public function indexAction()
//    {
//        prn(12);
//        return new ViewModel(['str' => 1]);
//    }

    protected function getPages($queryName)
    {
        $data     = [];
        $maxItems = 10;

        $query = $this->getQueryServiceVerify()->get($queryName)
            ->process();

        // model view should deal with acl enabled model
        $model    = $query->getModelName();
        $aclModel = $this->getAclServiceVerify()->getAclDataModel($model);

        $paginator
            = $this
            ->getGatewayServiceVerify()->get($model, $aclModel)
            ->getPages([], $query->getWhere(), $query->getOrder());

        if ($paginator->count() > 0) {
            $paginator
                ->setCurrentPageNumber($this->getParam('page', 1))
                ->setItemCountPerPage($this->getParam('rows', $maxItems));
        }

        $result = $query->getData();

        foreach ($paginator->getCurrentItems() as $row) {
            $data[] = $row->getArrayCopy();
        }

        $result += [
            'query' => $queryName,
            'model' => $model,
            'page'  => $paginator->getCurrentPageNumber(),
            'rows'  => $paginator->getItemCountPerPage(),
            'count' => $paginator->getTotalItemCount(),
            'data'  => $data,
        ];
        return $result;

    }

    public function getList()
    {
        $query = $this->params('scope', null);

//        prn(
//        [
//           md5('N1n0Bogat'),  md5('Sermorel!nGHRP#2')
//        ]
//        );
        if (null !== $query) {
            $data = $this->getPages(ucfirst($query));
        }

//        return new ViewModel($data);
        return new JsonModel($data);
    }

    public
    function get(
        $id
    ) {
        return new JsonModel([
            'id'    => '1',
            'title' => 'get lergerg2',
            'model' => 'lead'
        ]);
    }

    public
    function create(
        $data
    ) {
        # code...
    }

    public
    function update(
        $id,
        $data
    ) {
        # code...
    }

    public
    function delete(
        $id
    ) {
        # code...
    }

}
