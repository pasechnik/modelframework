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
use ModelFramework\ModelService\ModelService;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class ListDataController extends AbstractRestfulController
    implements ParamsAwareInterface, QueryServiceAwareInterface,
               GatewayServiceAwareInterface, AclServiceAwareInterface,
               LogicServiceAwareInterface, FormServiceAwareInterface,
               ModelServiceAwareInterface
{

    use ParamsAwareTrait, QueryServiceAwareTrait, GatewayServiceAwareTrait,
        AclServiceAwareTrait, LogicServiceAwareTrait, FormServiceAwareTrait,
        ModelServiceAwareTrait;

    public function setServiceLocator( ServiceLocatorInterface $serviceLocator )
    {
        parent::setServiceLocator( $serviceLocator );
        if (!$serviceLocator instanceof ControllerManager) {
            $this->setAclService( $serviceLocator->get( 'ModelFramework\AclService' ) );
            $this->setGatewayService( $serviceLocator->get( 'ModelFramework\GatewayService' ) );
            $this->setLogicService( $serviceLocator->get( 'ModelFramework\LogicService' ) );
            $this->setFormService( $serviceLocator->get( 'ModelFramework\FormService' ) );
            $this->setModelService( $serviceLocator->get( 'ModelFramework\ModelService' ) );

            $this->setParams( $this->params() );
            $query = $serviceLocator->get( 'ModelFramework\QueryService' );
            $query->setParams( $this->params() );
            $this->setQueryService( $query );
        }
    }


    /**
     * @param $form
     * @param $model
     *
     * @return mixed
     */
    protected function validateModel( $form, $model )
    {
        $bindData = [ ];
        foreach ($form->getValidationGroup() as $key => $fieldset) {
            if (!is_array( $fieldset )) {
                $bindData[ $key ] = $model->key;
            }
            foreach ($fieldset as $f_key => $f_value) {
                $bindData[ $key ][ $f_value ] = $model->$f_value;
            }
        }
        $form->setData( $bindData );
        $result = $form->isValid();
        if ($result) {
            $model_data = [ ];
            foreach ($form->getData() as $_k => $_data) {
                $model_data += is_array( $_data ) ? $_data :
                    [ $_k => $_data ];
            }
            $model->merge( $model_data );
        }

        return $result;
    }

    public function create( $data )
    {
        $modelName = ucfirst( $this->params( 'scope', null ) );
        $aclModel  =
            $this->getAclServiceVerify()->getAclDataModel( $modelName );
        $aclConfig = $aclModel->getDataPermissions();
        if (!in_array( 'create', $aclConfig->data )) {
            $this->response->setStatusCode( 403 );
            return new JsonModel( [ 'content' => 'Method is not allowed' ] );
        }
        $result = [];
        foreach ($data['data'] as $_key =>$_data) {
//            prn($data['data'],$_data);
            if(empty($_data)){continue;}
//            $aclModel  =
//                $this->getAclServiceVerify()->getAclDataModel( $modelName );
//            $aclConfig = $aclModel->getDataPermissions();
//            if (!in_array( 'create', $aclConfig->data )) {
//                $this->response->setStatusCode( 403 );
//                return new JsonModel( [ 'content' => 'Method is not allowed' ] );
//            }
            $aclModel->exchangeArray( $_data );
            $form  = $this->getFormServiceVerify()->get( $aclModel, 'create', [ ] );
            $valid = $this->validateModel( $form, $aclModel );
            if (!$valid) {
                $this->response->setStatusCode( 403 );

                return new JsonModel( [
                    'content' => 'Data is not valid',
                    'form'    => $form->getMessages(),
                ] );
            }
            $this->getLogicServiceVerify()->get( 'preinsert', $modelName )
                 ->trigger( $aclModel->getDataModel() );
            $dbResponse = $this->getGatewayServiceVerify()->get( $modelName )
                               ->save( $aclModel->getDataModel() );
            if ($dbResponse == null) {
                $this->response->setStatusCode( 500 );
                return new JsonModel( [ 'content' => 'Db error' ] );
            }
            $this->getLogicServiceVerify()->get( 'postinsert', $modelName )
                 ->trigger( $aclModel->getDataModel() );
            $_a         = $aclModel->getArrayCopy();
            $_a[ 'id' ] = $_a[ '_id' ];
            $result[]     = [
                'model'      => $modelName,
                'dbResponse' => $dbResponse,
                'data'       => $_a,
            ];
        }

        return new JsonModel( $result );
    }

//    public function update($id, $data)
//    {
//        $modelName = ucfirst($this->params('scope', null));
//        $aclModel  =
//            $this->getAclServiceVerify()->getAclDataModel( $modelName );
//        $query     =
//            $this->getQueryServiceVerify()->get( $modelName . 'Update' )
//                 ->process();
//        $result    = $query->getData();
//        $aclModel  =
//            $this->getGatewayServiceVerify()->get( $modelName, $aclModel )
//                 ->findOne( $query->getWhere() );
//        if ($aclModel == null) {
//            $this->response->setStatusCode(404);
//
//            return new JsonModel([
//                'content' => 'Model not found',
//            ]);
//        }
//        $aclConfig = $aclModel->getDataPermissions();
//        if (!in_array( 'write', $aclConfig->data )) {
//            $this->response->setStatusCode( 403 );
//            return new JsonModel( [
//                'content' => 'Method is not Allowed',
//            ] );
//        }
//        $aclModel->merge($data[ 'data' ]);
//        $form  = $this->getFormServiceVerify()->get($aclModel, 'update', [ ]);
//        $valid = $this->validateModel($form, $aclModel);
//        if (!$valid) {
//            $this->response->setStatusCode(400);
//
//            return [
//                'content' => 'Data is not valid',
//                'form'    => $form->getMessages(),
//            ];
//        }
//        $this->getLogicServiceVerify()->get( 'preupdate', $modelName )
//             ->trigger( $aclModel->getDataModel() );
//        $dbResponse = $this->getGatewayServiceVerify()->get( $modelName )
//                           ->save( $aclModel->getDataModel() );
//        if ($dbResponse == null) {
//            $this->response->setStatusCode( 500 );
//            return new JsonModel( [
//                'content' => 'Db error',
//            ] );
//        }
//        $this->getLogicServiceVerify()->get('postupdate', $modelName)
//             ->trigger($aclModel->getDataModel());
//        $_a         = $aclModel->getArrayCopy();
//        $_a[ 'id' ] = $_a[ '_id' ];
//        $result = [
//            'model'      => $modelName,
//            'dbResponse' => $dbResponse,
//            'data'       => $_a,
//        ];
//
//        return new JsonModel( $result );
//    }

}
