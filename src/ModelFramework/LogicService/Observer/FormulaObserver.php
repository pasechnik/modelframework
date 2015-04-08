<?php
/**
 * Class AgeObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class FormulaObserver extends AbstractConfigObserver
{
    public function process($model, $key, $value)
    {
        $result = '';

        eval('$result = '.$value.';');

        $model->$key = $result;
    }
}
