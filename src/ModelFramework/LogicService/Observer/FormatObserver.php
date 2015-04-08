<?php
/**
 * Class FormatObserver
 * @package ModelFramework\LogicService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class FormatObserver extends AbstractConfigObserver
{
    public function process($model, $key, $value)
    {
        if (!method_exists($this, $value)) {
            throw new \Exception('Unknown field type "'.$value.'"');
        }
        $this->$value($model, $key);
    }

    private function phone($model, $key)
    {
        $_value = preg_replace('/[^0-9]/', '', $model->$key);
        $phone  = '';
        if (strlen($_value) > 7) {
            $_cntry = substr($_value, -13, -10);
            $mask   = '%s(%s) %s-%s';
            if (strlen($_cntry)) {
                $mask = '+%s (%s) %s-%s';
            }
            $phone = sprintf($mask, $_cntry, substr($_value, -10, -7), substr($_value, -7, -4),
                              substr($_value, -4));
        }
        $model->$key = $phone;
    }

    private function card($model, $key)
    {
        $value = (string) $model->$key;

        $value = substr($value, -4);
//        for ( $i = 0; $i < strlen( $model->$key ) - 2; $i++ )
//        {
//            $value[ $i ] = '*';
//        }
        $model->$key = $value;
    }
}
