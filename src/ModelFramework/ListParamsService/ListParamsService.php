<?php

namespace ModelFramework\ListParamsService;

use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;
use ModelFramework\ViewService\ViewConfig\ViewConfig;

class ListParamsService implements ListParamsServiceInterface, GatewayServiceAwareInterface, ParamsAwareInterface,
                                   ConfigServiceAwareInterface
{

    use GatewayServiceAwareTrait, ParamsAwareTrait, ConfigServiceAwareTrait;

    private $_listParams = null;

    public function getGW()
    {
        if ($this->_listParams == null) {
            $this->_listParams = $this->getGatewayServiceVerify()->get( 'ListParams' );
        }
        return $this->_listParams;
    }

    public function getListParams( $hash )
    {
        if (!$hash) {
            return null;
        }
        $listParams = $this->getGW()->find( [ 'label' => $hash ] );
        if ($listParams->count() > 0) {
            $listParams = $listParams->current();
        } else {
            $listParams = null;
        }

        return $listParams;
    }

    public function getHash( $modelName, $qparams, $customParams = null )
    {
        $listParams = $this->getGW();
        $params     = $listParams->model();
        if ($customParams) {
            if (is_array( $customParams )) {
                if(!empty($customParams['hash'])){
                    $tmp = $this->getListParams($customParams['hash']);
                    if ($tmp){
                        $params = $tmp;
                    }
                }
                $params = $params->merge($customParams);
                $params->label = '';
            }
            else throw new \Exception('Wrong params');
            $hash = $this->checkParams( $params );
        } else {
            $params = $this->gatherParams( $params, $modelName, $qparams );
            $hash   = $this->checkParams( $params );
        }
        return $hash;
    }

    public function gatherParams( $model, $modelName, $qparams )
    {
        if ($qparams->fromRoute( 'view' ) != 'list' && $qparams->fromRoute( 'view' ) != null) {
            return null;
        }
        $modelName = ucfirst($modelName);
        $viewConfig    = $this->getConfigServiceVerify()->getByObject( $modelName . '.list',
            new ViewConfig() );
        if(!$viewConfig) return null;
        $model->rows   = $qparams->fromQuery( 'rowcount', $viewConfig->rows );
        $model->p      = $qparams->fromRoute( 'page', '1' );
        $model->sort   = $qparams->fromRoute( 'sort', 'created_dtm' );
        $model->desc   = $qparams->fromRoute( 'desc', '1' );
        $model->letter = $qparams->fromQuery( 'letter', null );
        $model->w      = $qparams->fromQuery( 'q', null );
        return $model;
    }

    public function checkParams( $model )
    {
        if ($model == null) {
            return null;
        }
        $checkParam = $this->_listParams->findOne( [
            'rows'   => $model->rows,
            'p'      => $model->p,
            'sort'   => $model->sort,
            'desc'   => $model->desc,
            'letter' => $model->letter,
            'w'      => $model->w,
        ] );
//        prn($checkParam);
        if ($checkParam) {
            return $checkParam->label;
        } else {
            $model->label = md5( md5( time() . uniqid() ) );
            $model->_id = null;
            try {
                $this->_listParams->save( $model );
            } catch ( \Exception $ex ) {
                $model->label = null;
            }
            return $model->label;
        }
    }
}
