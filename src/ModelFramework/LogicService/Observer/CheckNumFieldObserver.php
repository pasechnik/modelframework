<?php
/**
 * Class DateObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class CheckNumFieldObserver extends AbstractConfigObserver
{

    public function process( $model, $key, $value )
    {
        $value = $model->$key;
        $value       = str_replace( ' ', '', $value );
        $value       = str_replace( ',', '.', $value );
        $model->$key = date( $value );
    }
}
