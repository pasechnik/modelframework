<?php
/**
 * Class ChangerObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class ChangerObserver extends AbstractConfigObserver
{
    public function process($model, $key, $value)
    {
        $model->$key = $this->getSubject()->getAuthServiceVerify()->getUser()->id();
    }
}
