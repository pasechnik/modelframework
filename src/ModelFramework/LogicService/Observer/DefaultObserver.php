<?php
/**
 * Class ConstantObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class DefaultObserver extends AbstractConfigObserver
{

    public function process($model, $key, $value)
    {
        if (empty($model->$key)) {
            $model->$key = $value;
        }
    }
}
