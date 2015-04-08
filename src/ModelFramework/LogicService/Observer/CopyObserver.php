<?php
/**
 * Class CopyObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class CopyObserver extends AbstractConfigObserver
{
    public function process($model, $key, $value)
    {
        $model->$key = $model->$value;
    }
}
