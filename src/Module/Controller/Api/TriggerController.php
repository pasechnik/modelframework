<?php

namespace Wepo\Controller\Api;

use ModelFramework\AclService\AclServiceAwareInterface;
use ModelFramework\AclService\AclServiceAwareTrait;
use ModelFramework\FormService\FormServiceAwareInterface;
use ModelFramework\FormService\FormServiceAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\LogicService\LogicServiceAwareInterface;
use ModelFramework\LogicService\LogicServiceAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\JsonModel;

class TriggerController extends AbstractRestfulController
    implements ParamsAwareInterface, QueryServiceAwareInterface,
               GatewayServiceAwareInterface, AclServiceAwareInterface,
               LogicServiceAwareInterface, FormServiceAwareInterface
{

    use ParamsAwareTrait, QueryServiceAwareTrait, GatewayServiceAwareTrait,
        AclServiceAwareTrait, LogicServiceAwareTrait, FormServiceAwareTrait;

    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        parent::setServiceLocator( $serviceLocator );
        if (!$serviceLocator instanceof ControllerManager) {
            $this->setAclService( $serviceLocator->get( 'ModelFramework\AclService' ) );
            $this->setGatewayService( $serviceLocator->get( 'ModelFramework\GatewayService' ) );
            $this->setLogicService( $serviceLocator->get( 'ModelFramework\LogicService' ) );
            $this->setParams( $this->params() );
            $query = $serviceLocator->get( 'ModelFramework\QueryService' );
            $query->setParams( $this->params() );
            $this->setQueryService( $query );
        }
    }

    public function get( $id )
    {
        $modelName = ucfirst( $this->params( 'scope', null ) );
        $logicName = $this->getParam( 'logic', null );
        if (!$logicName) {
            $this->response->setStatusCode( 404 );
            return new JsonModel( [
                'content' => 'Please set logic name in logic param',
            ] );
        }
        $aclModel =
            $this->getAclServiceVerify()->getAclDataModel( $modelName );
        $query    = $this->getQueryServiceVerify()->get( $modelName . 'View' )
                         ->process();
        $result   = $query->getData();
        $aclModel =
            $this->getGatewayServiceVerify()->get( $modelName, $aclModel )
                 ->findOne( $query->getWhere() );
        if (!$aclModel) {
            $this->response->setStatusCode( 404 );
            return new JsonModel( [
                'content' => 'Model not found',
            ] );
        }
        $aclConfig = $aclModel->getDataPermissions();
        if (!in_array( 'read', $aclConfig->data )) {
            $this->response->setStatusCode( 403 );
            return new JsonModel( [
                'content' => 'Method is not allowed',
            ] );
        }
        $this->getLogicServiceVerify()->get( $logicName, $modelName )
             ->trigger( $aclModel );
        $_a         = $aclModel->getArrayCopy();
        $_a[ 'id' ] = $_a[ '_id' ];
        $result += [
            'model' => $modelName,
            'logic' => $logicName,
            'data'  => $_a,
        ];

        return new JsonModel( $result );
    }

//    public function getList()
//    {
//    }
//
//    public function create( $data )
//    {
//    }
//
//    public function update( $id, $data )
//    {
//    }
//
//    public function delete( $id )
//    {
//    }

}
