<?php
/**
 * Class UniqidObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class UniqidObserver extends AbstractConfigObserver
{
    public function process($model, $key, $value)
    {
        if (empty($model->$key)) {
            $model->$key = substr(str_replace('.','',hexdec(md5(uniqid()))),0,12);
        }
    }
}
