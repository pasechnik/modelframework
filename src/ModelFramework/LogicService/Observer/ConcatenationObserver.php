<?php
/**
 * Class ConcatenationObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class ConcatenationObserver extends AbstractConfigObserver
{

    public function process( $model, $key, $value )
    {
        $concatValue = '';
        foreach ($value as $value_key) {
            if (strlen( $concatValue )) {
                $concatValue .= ' ';
            }
            $concatValue .= (string) $model->$value_key;
        }
        $model->$key = $concatValue;
    }
}
