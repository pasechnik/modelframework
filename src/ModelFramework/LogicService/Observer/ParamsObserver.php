<?php
/**
 * Class ParamsObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class ParamsObserver extends AbstractConfigObserver
{
    use SubjectAwareTrait;

    public function process($model, $key, $value)
    {
        $param = $this->getSubject()->getParam($value, null);
        if ($param != null) {
            $model->$key = $param;
        }
    }
}
