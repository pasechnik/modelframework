<?php
/**
 * Class AttachObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\AclService\AclDataModel;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;
use ModelFramework\ViewService\View;
use Wepo\Lib\Acl;

class AttachObserver implements \SplObserver, ConfigAwareInterface, SubjectAwareInterface
{
    use ConfigAwareTrait, SubjectAwareTrait;

    private $_aclModel = null;
    private $_model = null;

    /**
     * @param \SplSubject|View $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);
        $subject->getLogicServiceVerify()->setParams($subject->getParams());

        $dataModel = $this->initModel();

//        $form = $this->initForm();

        $this->process(null, $this->getModel());

        $model = $this->setModel($dataModel);
    }

    public function getModel()
    {
        if ($this->_aclModel !== null) {
            return $this->_aclModel;
        }

        return $this->_model;
    }

    public function getModelData()
    {
        return $this->_model;
    }

    public function initModel()
    {
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        $query      =
            $subject->getQueryServiceVerify()
                    ->get($viewConfig->query)
                    ->setParams($subject->getParams())
                    ->process();

        $data = $subject->getData();

        if (isset($data[ 'model' ]) && $data[ 'model' ] instanceof DataModelInterface) {
            $model = $data[ 'model' ];
        } else {
            if ($viewConfig->mode == 'insert') {
                $model = $subject->getGateway()->model();
//                $model = $query->setDefaults( $model );
            } elseif ($viewConfig->mode == 'update') {
                $model = $subject->getGateway()->findOne($query->getWhere());
                if ($model == null) {
                    throw new \Exception('Data is not accessible');
                }
            } else {
                throw new \Exception("Wrong mode  '".$viewConfig->mode."' in  ".$viewConfig->key.
                                      ' View Config for the '.get_class());
            }
        }

        if ($model instanceof AclDataModel) {
            $this->_aclModel = $model;
            $this->_model    = $this->_aclModel->getDataModel();
        }

        $subject->getLogicServiceVerify()->get('setDefaults', $model->getModelName())->trigger($this->_model);

        return $this->_model;
    }

    public function setModel(DataModelInterface $model)
    {
        if ($this->_aclModel !== null && $this->_aclModel instanceof AclDataModel) {
            $this->_aclModel->setDataModel($model);
            $model = $this->_aclModel;
        }

        $this->getSubject()->setData([ 'model' => $model ]);

        return $model;
    }

    public function initForm()
    {
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        if ($viewConfig->mode == 'insert') {
            $mode = Acl::MODE_CREATE;
        }

        if ($viewConfig->mode == 'update') {
            $mode = Acl::MODE_EDIT;
        }

        $form = $subject->getFormServiceVerify()->get($this->getModel(), $mode);
        $form->setRoute('common');
        $form->setActionParams([ 'data' => strtolower($viewConfig->model), 'view' => $viewConfig->mode ]);

        if ($this->getModel()->id() !== '') {
            $form->setActionParams([ 'id' => $this->getModel()->id() ]);
        }

        if (isset($form->getFieldsets()[ 'saurl' ])) {
            $form->getFieldsets()[ 'saurl' ]->get('back')->setValue($subject->getParams()
                                                                               ->fromQuery('back', 'home'));
        }

        return $form;
    }

    /**
     * @param $form
     * @param $model
     */
    public function process($form, $model)
    {
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();

        $results = [ ];
//        $old_data = $model->split( $form->getValidationGroup() );

        //Это жесть конечно и забавно, но на время сойдет :)
        $model_bind = $model->toArray();
        foreach ($model_bind as $_k => $_v) {
            if (substr($_k, -4) == '_dtm') {
                $model->$_k = str_replace(' ', 'T', $_v);
            }
        }
        //Конец жести

        $request = $subject->getParams()->getController()->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();

//            $attach_to       = $model->attach_to;
//            $attach_to_names = $model->attach_to_names;
//            if (!empty($post[ 'attach_lead' ])) {
//                $leads      = explode(',', $post[ 'attach_lead' ]);
//                $leadsnames = [ ];
//                foreach ($leads as $_lead) {
//                    $tmp           = $subject->getGatewayServiceVerify()->get('Lead')->findOne([ '_id' => $_lead ]);
//                    $leadsnames[ ] = $tmp->title;
//                }
//            } else {
//                $leads      = $attach_to[ 'Lead' ];
//                $leadsnames = $attach_to_names[ 'Lead' ];
//            }
//            if (!empty($post[ 'attach_patient' ])) {
//                $patients      = explode(',', $post[ 'attach_patient' ]);
//                $patientsnames = [ ];
//                foreach ($patients as $_patient) {
//                    $tmp              =
//                        $subject->getGatewayServiceVerify()->get('Patient')->findOne([ '_id' => $_patient ]);
//                    $patientsnames[ ] = $tmp->title;
//                }
//            } else {
//                $patients      = $attach_to[ 'Patient' ];
//                $patientsnames = $attach_to_names[ 'Patient' ];
//            }
//            $attach_to[ 'Lead' ]          = $leads;
//            $attach_to[ 'Patient' ]       = $patients;
//            $attach_to_names[ 'Lead' ]    = $leadsnames;
//            $attach_to_names[ 'Patient' ] = $patientsnames;
//
            $exchange = $model->getDataModel();
//            $exchange->attach_to          = $attach_to;
//            $exchange->attach_to_names    = $attach_to_names;
            $model->setDataModel($exchange);
        }

        $data[ 'model' ] = $model;
        $subject->setData($data);
    }
}
