<?php
/**
 * Class DateObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class DateObserver extends AbstractConfigObserver
{
    public function process($model, $key, $value)
    {
        $model->$key = date($value);
    }
}
