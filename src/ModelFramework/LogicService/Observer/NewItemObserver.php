<?php
/**
 * Class NewItemObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use Wepo\Model\Status;

class NewItemObserver extends AbstractConfigObserver
{
    public function process($model, $key, $value)
    {
        $modelName = $model->getModelName();
        if ($value < 0 && isset($model->status_id) && $model->status_id != Status::NEW_) {
            return;
        }
        if( ($value>0 && $model->status_id != Status::NEW_) && ($model->status_id != Status::INPROGRESS && $value>0 )){
            return;
        }
        $id       = $model->$key;
        $user     =
            $this->getSubject()->getGatewayServiceVerify()->get('User')->findOne([ '_id' => $id ]);
        $newItems = [ ];
        if (isset($user->newitems)) {
            $newItems = $user->newitems;
        }
        if (!isset($newItems[ $modelName ])) {
            $newItems[ $modelName ] = 0;
        }
        $newItems[ $modelName ] = (int) $newItems[ $modelName ];

        if ($newItems[ $modelName ] + $value < 0) {
            return;
        }
        $newItems[ $modelName ] += $value;
        $this->getSubject()->getGatewayServiceVerify()->get('User')
             ->update([ 'newitems' => $newItems ], [ '_id' => $id ]);
    }
}
