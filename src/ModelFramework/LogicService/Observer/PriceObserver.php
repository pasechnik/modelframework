<?php
/**
 * Class AclObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

//fixme test needs after quote, order, invoice add functionality created
use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class PriceObserver
    implements \SplObserver, ConfigAwareInterface, SubjectAwareInterface
{
    use ConfigAwareTrait, SubjectAwareTrait;

    private $defaultConfigs = [
        'list_price'     => [ 'value' => 0, 'field' => 'list_price' ],
        'qty'           => [ 'value' => 1, 'field' => 'qty' ],
        'amount'        => [ 'value' => 0, 'field' => 'amount' ],
        'discount'      => [ 'value' => 0, 'field' => 'discount' ],
        'tax'           => [ 'value' => 0, 'fields' => [ 'vat', 'sales_tax' ], 'field' => 'tax' ],
//        'adjustment'    => [ 'value' => 0, 'field' => 'adjustment' ],
        'total'  => [ 'value' => 0, 'field' => 'total' ],
    ];

    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);

        $models = $subject->getEventObject();
        if (!(is_array($models) || $models instanceof ResultSetInterface)) {
            $models = [ $models ];
        }

        $aModels = [ ];
        foreach ($models as $_k => $model) {
            $config = $this->updateDefaultConfigs($model);

            $total_price = $config[ 'raw_price' ][ 'value' ] * $config[ 'qty' ][ 'value' ];
            if ($config[ 'discount_type' ][ 'value' ] == '% of Price') {
                $total_price *= (1 - $config[ 'discount' ][ 'value' ] / 100);
            }

            if ($config[ 'discount_type' ][ 'value' ] == 'Direct Price Reduction') {
                $total_price -=  $config[ 'discount' ][ 'value' ];
            }

            $total_price = round($total_price, 2);

            $taxes       = $total_price * $config[ 'tax' ][ 'value' ] / 100;
            $total_price += $taxes;
            $total_price += $config[ 'adjustment' ][ 'value' ];

            $model->$config[ 'tax' ][ 'field' ]          = $taxes;
            $model->$config[ 'result_price' ][ 'field' ] = $total_price;

            $aModels[ ] = $model->getArrayCopy();
        }

        if ($models instanceof ResultSetInterface) {
            $models->initialize($aModels);
        }
    }

    protected function updateDefaultConfigs($model)
    {
        $config     = $this->defaultConfigs;
        $usrConfigs = $this->getRootConfig();
        foreach ($config as $key => $value) {
            if (isset($usrConfigs[ $key ])) {
                $config[ $key ][ 'field' ] = $usrConfigs[ $key ];
            }
            if ($key == 'tax') {
                foreach ($value['fields'] as $field) {
                    $config[ $key ][ 'value' ] += $model->$field;
                }
            } else {
                $config[ $key ][ 'value' ] =
                    isset($model->$config[ $key ][ 'field' ]) ? $model->$config[ $key ][ 'field' ] : $config[ $key ][ 'value' ];
            }
        }

        return $config;
    }
}
