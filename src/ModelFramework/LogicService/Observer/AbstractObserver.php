<?php
/**
 * Class AbstractObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\AclService\AclDataModel;
use ModelFramework\LogicService\Logic;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;
use Zend\Db\ResultSet\ResultSetInterface;

abstract class AbstractObserver
    implements \SplObserver, SubjectAwareInterface
{
    use SubjectAwareTrait;

    /**
     * @param \SplSubject|Logic $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);

        $models = $subject->getEventObject();
        if (!(is_array($models)
            || $models instanceof ResultSetInterface)
        ) {
            $models = [ $models ];
        }

        $aModels = [ ];
        foreach ($models as $_k => $model) {
            if ($model instanceof AclDataModel) {
                $dataModel = $model->getDataModel();
            } else {
                $dataModel = $model;
            }

            $this->processModel($dataModel);

            $aModels[ ] = $model->getArrayCopy();
        }

        if ($models instanceof ResultSetInterface) {
            $models->initialize($aModels);
        }
    }

    abstract public function processModel($model);
}
