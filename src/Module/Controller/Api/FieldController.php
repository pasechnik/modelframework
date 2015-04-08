<?php

namespace Wepo\Controller\Api;

use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\JsonModel;

class FieldController extends AbstractRestfulController
    implements ParamsAwareInterface, GatewayServiceAwareInterface,
               ConfigServiceAwareInterface
{

    use ParamsAwareTrait, GatewayServiceAwareTrait, ConfigServiceAwareTrait;

    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        parent::setServiceLocator( $serviceLocator );
        if (!$serviceLocator instanceof ControllerManager) {
            $this->setGatewayService( $serviceLocator->get( 'ModelFramework\GatewayService' ) );
            $this->setParams( $this->params() );
        }
    }

    public function getList()
    {
        $modelName = ucfirst( $this->params( 'scope', null ) );
        $model     = $this->getGatewayServiceVerify()->get( 'Model' )
                          ->findOne( [ 'model' => $modelName ] );
        $data      = [ ];
        foreach ($model->fields as $_key =>
                 $_item) {
            $data[ $_key ] = $_item;
        }
        $data = [
            'data' => $data
        ];

        return new JsonModel( $data );
    }

    public function get( $id )
    {
        $modelName                  = ucfirst( $this->params( 'scope', null ) );
        $fieldName                  = $this->params( 'id', null );
        $model                      =
            $this->getGatewayServiceVerify()->get( 'Model' )
                 ->findOne( [ 'model' => $modelName ] );
        $fieldConfig                = $model->fields[ $fieldName ];
        $result[ 'data' ][ 'name' ] = $fieldName;
        $result[ 'data' ][ 'conf' ] = $fieldConfig;

        return new JsonModel( $result );
    }

    public function create( $data )
    {
        $modelName                    =
            ucfirst( $this->params( 'scope', null ) );
        $model                        =
            $this->getGatewayServiceVerify()->get( 'Model' )
                 ->findOne( [ 'model' => $modelName ] );
        $model->fields[ $data->name ] = $data->conf;
        $result[ 'dbResult' ]         =
            $this->getGatewayServiceVerify()->get( 'Model' )->save( $model );

        return new JsonModel( $result );
    }

    public function update( $id, $data )
    {
        $modelName                   =
            ucfirst( $this->params( 'scope', null ) );
        $fieldName                   = $this->params( 'id', null );
        $model                       =
            $this->getGatewayServiceVerify()->get( 'Model' )
                 ->findOne( [ 'model' => $modelName ] );
        $model->fields[ $fieldName ] = $data->conf;
        $result[ 'dbResult' ]        =
            $this->getGatewayServiceVerify()->get( 'Model' )->save( $model );

        return new JsonModel( $result );
    }

    public function delete( $id )
    {
        $modelName =
            ucfirst( $this->params( 'scope', null ) );
        $fieldName = $this->params( 'id', null );
        $model     =
            $this->getGatewayServiceVerify()->get( 'Model' )
                 ->findOne( [ 'model' => $modelName ] );
        unset( $model->fields[ $fieldName ] );
        $result[ 'dbResult' ] =
            $this->getGatewayServiceVerify()->get( 'Model' )->save( $model );

        return new JsonModel( $result );
    }
}
