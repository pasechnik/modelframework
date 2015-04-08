<?php
/**
 * Class SaveObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\AclService\AclDataModel;
use ModelFramework\LogicService\Logic;
use Zend\Db\ResultSet\ResultSetInterface;

class SaveObserver
    implements \SplObserver
{
    /**
     * @param \SplSubject|Logic $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {

        $models = $subject->getEventObject();
        if (!(is_array($models) || $models instanceof ResultSetInterface)) {
            $models = [ $models ];
        }
        foreach ($models as $_k => $model) {
            if ($model instanceof AclDataModel) {
                $dataModel = $model->getDataModel();
            } else {
                $dataModel = $model;
            }
            $subject->getGatewayServiceVerify()->get($dataModel->getModelName())->save($dataModel);
        }
    }
}
