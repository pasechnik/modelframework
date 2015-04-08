<?php
/**
 * Class OwnerObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class OwnerObserver extends AbstractConfigObserver
{
    public function process($model, $key, $value)
    {
        if (empty($model->$key)) {
            $model->$key = $this->getSubject()->getAuthServiceVerify()->getUser()->id();
        }
    }
}
