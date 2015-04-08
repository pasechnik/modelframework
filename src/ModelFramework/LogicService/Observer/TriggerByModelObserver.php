<?php
/**
 * Class DateObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class TriggerByModelObserver extends AbstractConfigObserver
{
    use SubjectAwareTrait;

    public function process($model, $key, $value)
    {
        $search_key = $value['key'];
        $modelname = $value['modelname'];
        $logic = $value['logic'];
        $details = $this->getSubject()->getGatewayServiceVerify()->get($modelname)->find([$search_key=>$model->$key]);
        foreach($details as $detail){
            $runlogic = $this->getSubject()->getLogicServiceVerify()->get($logic, $modelname);
            $runlogic->trigger($detail);
        }
    }
}
