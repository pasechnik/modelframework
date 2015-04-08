<?php
/**
 * Class FormObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use Wepo\Lib\Acl;

class FormObserver extends AbstractObserver
{

    public function process( $model )
    {
        $form = $this->initForm();
        $this->processForm( $form, $this->getModel() );
    }

    public function initForm()
    {
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        $form       = $subject->getFormServiceVerify()
                              ->get( $this->getModel(), $viewConfig->mode,
                                  $viewConfig->fields );
        $form->setRoute( 'common' );
        $form->setActionParams( [
            'data' => strtolower( $viewConfig->model ),
            'view' => $viewConfig->mode
        ] );
        if ($this->getModel()->id() !== '') {
            $form->setActionParams( [ 'id' => $this->getModel()->id() ] );
        }
        if (isset( $form->getFieldsets()[ 'saurl' ] )) {
            $form->getFieldsets()[ 'saurl' ]->get( 'back' )
                                            ->setValue( $subject->getParams()
                                                                ->fromQuery( 'back',
                                                                    'home' ) );
        }

        return $form;
    }

    /**
     * @param $form
     * @param $model
     */
    public function processForm( $form, $model )
    {
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        $results    = [ ];
        $old_data   = $model->split( $form->getValidationGroup() );
        //Это жесть конечно и забавно, но на время сойдет :)
        $model_bind = $model->toArray();
        $fieldsAcl  = $model->getAclConfig()->fields;
        foreach ($model_bind as $_k => $_v) {
            if (substr( $_k, -4 ) == '_dtm' && $fieldsAcl[ $_k ] == 'write') {
                $model->$_k = str_replace( ' ', 'T', $_v );
            }
        }
        //Конец жести
        $request = $subject->getParams()->getController()->getRequest();
        if ($request->isPost()) {
            $form->setData( $request->getPost() );
//            prn($request->getFiles(), $request->getPost(), $form->isValid(), $form->getValidationGroup());
//            exit;
            if ($form->isValid()) {
                $model_data = [ ];
                foreach ($form->getData() as $_k => $_data) {
                    $model_data += is_array( $_data ) ? $_data :
                        [ $_k => $_data ];
                }
                $model->merge( $model_data );
                $model->merge( $old_data );
                $subject->getLogicServiceVerify()->get( 'pre'
                                                        . $viewConfig->mode,

                    $model->getModelName() )
                        ->trigger( $model->getDataModel() );
               // prn($model->toArray());exit;
                try {
                    $subject->getGateway()->save( $model->getDataModel() );
                } catch ( \Exception $ex ) {
                    $results[ 'message' ]
                        = 'Invalid input data.' . $ex->getMessage();
                }
                if (!isset( $results[ 'message' ] )
                    || !strlen( $results[ 'message' ] )
                ) {
                    $subject->getLogicServiceVerify()->get( 'post'
                                                            . $viewConfig->mode,
                        $model->getModelName() )
                            ->trigger( $model->getDataModel() );
                    $url = $subject->getBackUrl();
//                    prn($url, $subject->getParams()->getController()->url());
//                    exit();
                    if(isset($form->getActionParams()['view']) && $form->getActionParams()['view'] == 'insert'){
                        $url ='/'.$form->getRoute().'/'.$form->getActionParams()['data'].'/view/'.$model->id;
                    }
                    if ($url == null || $url == '/') {
                        $url = $subject->getParams()->getController()->url()
                                       ->fromRoute( $form->getRoute(),
                                           $form->getActionParams() );
                    }
                    $subject->setRedirect( $subject->refresh( $model->getModelName()
                                                              .
                                                              ' data was successfully saved',
                        $url ) );

                    return;
                }
            }
        } else {
//            $_formElement->options['value_options'] = $options;

//            prn($form);
//            prn($model->toArray());
            $form->bind( $model );
        }
        $form->prepare();
        $results[ 'form' ] = $form;
        $subject->setData( $results );
    }
}
