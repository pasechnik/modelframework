<?php
/**
 * Class ParseLinkObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class ParseLinkObserver extends AbstractConfigObserver
{
    public function process($model, $key, $value)
    {
        if (!empty($model->$key) && empty(parse_url($model->$key)['scheme'])){
            $model->$key = 'http://'.$model->$key;
        }
    }
}
