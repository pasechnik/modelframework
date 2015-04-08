<?php
/**
 * Class FormObserver
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

abstract class AbstractObserver implements \SplObserver, ConfigAwareInterface, SubjectAwareInterface
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

        $this->process($this->getModel());

        $this->setModel($dataModel);
    }

    abstract public function process($model);

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
            } else {
                //            elseif ( in_array($viewConfig->mode, ['update', 'convert' ] ) )
                $model = $subject->getGateway()->findOne($query->getWhere());
//                prn($query->getWhere());
//                exit;
                if ($model == null) {
                    throw new \Exception('Data is not accessible');
                }
            }
//        else
//        {
//          throw new \Exception( "Wrong mode  '" . $viewConfig->mode . "' in  " . $viewConfig->key .
//              ' View Config for the ' . get_class() );
//        }
        }

        if ($model instanceof AclDataModel) {
            $this->_aclModel = $model;
            $subject -> setDataModel( $model );
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
}
