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

class DataController extends AbstractRestfulController
    implements ParamsAwareInterface, QueryServiceAwareInterface,
               GatewayServiceAwareInterface, AclServiceAwareInterface,
               LogicServiceAwareInterface, FormServiceAwareInterface,
               ModelServiceAwareInterface
{

    use ParamsAwareTrait, QueryServiceAwareTrait, GatewayServiceAwareTrait,
        AclServiceAwareTrait, LogicServiceAwareTrait, FormServiceAwareTrait,
        ModelServiceAwareTrait;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        parent::setServiceLocator($serviceLocator);
        if (!$serviceLocator instanceof ControllerManager) {
            $this->setAclService( $serviceLocator->get( 'ModelFramework\AclService' ) );
            $this->setGatewayService( $serviceLocator->get( 'ModelFramework\GatewayService' ) );
            $this->setLogicService( $serviceLocator->get( 'ModelFramework\LogicService' ) );
            $this->setFormService( $serviceLocator->get( 'ModelFramework\FormService' ) );
            $this->setModelService( $serviceLocator->get( 'ModelFramework\ModelService' ) );

            $this->setParams($this->params());
            $query = $serviceLocator->get('ModelFramework\QueryService');
            $query->setParams($this->params());
            $this->setQueryService($query);
        }
    }

    protected function getPages( $model )
    {
        $data     = [ ];
        $maxItems = 10;
        $queryName = $this->getParam('query', $model . '.list');
        $query    = $this->getQueryServiceVerify()->get( $queryName )
                         ->process();
        $aclModel = $this->getAclServiceVerify()->getAclDataModel( $model );
        $paginator
                  = $this
            ->getGatewayServiceVerify()->get( $model, $aclModel )
            ->getPages( [ ], $query->getWhere(), $query->getOrder() );
        if ($paginator->count() > 0) {
            $paginator
                ->setCurrentPageNumber($this->getParam('p', 1))
                ->setItemCountPerPage($this->getParam('rows', $maxItems));
        }
        $result = $query->getData();
        $this->getLogicServiceVerify()->get( 'prelist', $model )
             ->trigger( $paginator->getCurrentItems() );
        $i = 1;
        foreach ($paginator->getCurrentItems() as $row) {
            $_a         = $row->getArrayCopy();
            $_a[ 'id' ] = $_a[ '_id' ];
            $data[ ]    = $_a;
        }
        $this->getLogicServiceVerify()->get( 'postlist', $model )
             ->trigger( $paginator->getCurrentItems() );
        $result += [
            'query' => $query->getWhere(),
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
        $model = $this->params('scope', null);

        if (null !== $model) {
            $data = $this->getPages( ucfirst( $model ) );
        } else {
            $data = [ ];
            foreach ($this->getModelService()->getAllModelNames() as $_key =>
                     $_value) {
                $data[ strtolower( $_value ) ] = $_value;
            }
            $data = [
                'data' => $data
            ];
        }

        return new JsonModel($data);
    }

    public
    function get(
        $id
    ) {
        $modelName = ucfirst( $this->params( 'scope', null ) );
        $aclModel  =
            $this->getAclServiceVerify()->getAclDataModel( $modelName );
        $query     = $this->getQueryServiceVerify()->get( $modelName . 'View' )
                          ->process();
        $result    = $query->getData();
        $aclModel  =
            $this->getGatewayServiceVerify()->get( $modelName, $aclModel )
                 ->findOne( $query->getWhere() );
        if (!$aclModel) {
            $this->response->setStatusCode( 404 );
            return new JsonModel( [
                'content' => 'Model not found',
            ]);
        }
        $aclConfig = $aclModel->getDataPermissions();
        if (!in_array( 'read', $aclConfig->data )) {
            $this->response->setStatusCode( 403 );
            return new JsonModel( [
                'content' => 'Method is not allowed',
            ] );
        }
        $this->getLogicServiceVerify()->get( 'preview', $modelName )
             ->trigger( $aclModel );
        $this->getLogicServiceVerify()->get( 'postview', $modelName )
             ->trigger( $aclModel );
        $_a         = $aclModel->getArrayCopy();
        $_a[ 'id' ] = $_a[ '_id' ];
        $result += [
            'model' => $modelName,
            'page'  => 1,
            'rows'  => 1,
            'count' => 1,
            'data'  => $_a,
        ];

        return new JsonModel($result);
    }

    /**
     * @param $form
     * @param $model
     *
     * @return mixed
     */
    protected function validateModel($form, $model)
    {
        $bindData = [ ];
        foreach ($form->getValidationGroup() as $key => $fieldset) {
            if (!is_array($fieldset)) {
                $bindData[ $key ] = $model->key;
            }
            foreach ($fieldset as $f_key => $f_value) {
                $bindData[ $key ][ $f_value ] = $model->$f_value;
            }
        }
        $form->setData($bindData);
        $result = $form->isValid();
        if ($result) {
            $model_data = [ ];
            foreach ($form->getData() as $_k => $_data) {
                $model_data += is_array($_data) ? $_data :
                    [ $_k => $_data ];
            }
            $model->merge($model_data);
        }

        return $result;
    }

    public function create($data)
    {
        $modelName = ucfirst($this->params('scope', null));
        $aclModel  =
            $this->getAclServiceVerify()->getAclDataModel($modelName);
        $aclConfig = $aclModel->getDataPermissions();
        if (!in_array( 'create', $aclConfig->data )) {
            $this->response->setStatusCode( 403 );
            return new JsonModel( [ 'content' => 'Method is not allowed' ] );
        }
        $aclModel->merge($data[ 'data' ]);
        $form  = $this->getFormServiceVerify()->get($aclModel, 'create', [ ]);
        $valid = $this->validateModel($form, $aclModel);
        if (!$valid) {
            $this->response->setStatusCode(403);

            return new JsonModel([
                'content' => 'Data is not valid',
                'form'    => $form->getMessages(),
            ]);
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
        $result = [
            'model'      => $modelName,
            'dbResponse' => $dbResponse,
            'data'       => $_a,
        ];

        return new JsonModel($result);
    }

    public function update($id, $data)
    {
        $modelName = ucfirst($this->params('scope', null));
        $aclModel  =
            $this->getAclServiceVerify()->getAclDataModel( $modelName );
        $query     =
            $this->getQueryServiceVerify()->get( $modelName . 'Update' )
                 ->process();
        $result    = $query->getData();
        $aclModel  =
            $this->getGatewayServiceVerify()->get( $modelName, $aclModel )
                 ->findOne( $query->getWhere() );
        if ($aclModel == null) {
            $this->response->setStatusCode(404);

            return new JsonModel([
                'content' => 'Model not found',
            ]);
        }
        $aclConfig = $aclModel->getDataPermissions();
        if (!in_array( 'write', $aclConfig->data )) {
            $this->response->setStatusCode( 403 );
            return new JsonModel( [
                'content' => 'Method is not Allowed',
            ] );
        }
        $aclModel->merge($data[ 'data' ]);
        $form  = $this->getFormServiceVerify()->get($aclModel, 'update', [ ]);
        $valid = $this->validateModel($form, $aclModel);
        if (!$valid) {
            $this->response->setStatusCode(400);

            return [
                'content' => 'Data is not valid',
                'form'    => $form->getMessages(),
            ];
        }
        $this->getLogicServiceVerify()->get( 'preupdate', $modelName )
             ->trigger( $aclModel->getDataModel() );
        $dbResponse = $this->getGatewayServiceVerify()->get( $modelName )
                           ->save( $aclModel->getDataModel() );
        if ($dbResponse == null) {
            $this->response->setStatusCode( 500 );
            return new JsonModel( [
                'content' => 'Db error',
            ] );
        }
        $this->getLogicServiceVerify()->get('postupdate', $modelName)
             ->trigger($aclModel->getDataModel());
        $_a         = $aclModel->getArrayCopy();
        $_a[ 'id' ] = $_a[ '_id' ];
        $result = [
            'model'      => $modelName,
            'dbResponse' => $dbResponse,
            'data'       => $_a,
        ];

        return new JsonModel( $result );
    }

    public
    function delete(
        $id
    ) {
        $modelName = ucfirst( $this->params( 'scope', null ) );
        if ($modelName == null) {
            $this->response->setStatusCode( 400 );
            return new JsonModel( [
                'content' => 'Data scope is not set',
            ] );
        }
        $aclModel  =
            $this->getAclServiceVerify()->getAclDataModel( $modelName );
        $aclConfig = $aclModel->getDataPermissions();
        if (!in_array( 'delete', $aclConfig->data )) {
            $this->response->setStatusCode( 403 );
            return new JsonModel( [
                'content' => 'Method is not Allowed',
            ] );
        }
        $items  = [ ];
        $query  = $this->getQueryServiceVerify()->get( $modelName . 'Delete' )
                       ->process();
        $result = $query->getData();
        $model  = $this->getGatewayServiceVerify()->get( $modelName, $aclModel )
                       ->findOne( $query->getWhere() );
        if ($aclModel == null) {
            $this->response->setStatusCode( 404 );
            return new JsonModel( [
                'content' => 'Model not found',
            ] );
        }
        if ($model !== null) {
            $items[ $id ] = $model;
        }
        $this->getLogicServiceVerify()->get( 'predelete', $modelName )
             ->trigger( $items );
        $this->getLogicServiceVerify()->get( 'delete', $modelName )
             ->trigger( $items );
        $this->getLogicServiceVerify()->get( 'postdelete', $modelName )
             ->trigger( $items );
        $result += [
            'model' => $modelName,
            'page'  => 1,
            'rows'  => count( $items ),
            'count' => count( $items ),
            'data'  => [ ],
        ];

        return new JsonModel( $result );
    }
}
