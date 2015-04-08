<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:09
 */

namespace ModelFramework\FormService\FormField\Strategy;

use ModelFramework\FieldTypesService\FormElementConfig\FormElementConfigInterface;
use ModelFramework\FieldTypesService\InputFilterConfig\InputFilterConfigInterface;
use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfigInterface;
use Wepo\Model\Status;

class LookupStrategy extends AbstractFormFieldStrategy
{

    public function s(
        FieldConfigInterface $conf,
        FormElementConfigInterface $_formElement,
        InputFilterConfigInterface $_inputFilter
    ) {
//        prn( 'LookupStrategy ->s()', $conf, $_formElement, $_inputFilter );
        $name = $this->getName() . '_id';
        if (!$this->isAllowed( $name ) || !$this->isNotLimited( $name )) {
            return [
                'elements' => [ ],
                'filters'  => [ ]
            ];
        }
        $_inputFilter->name               = $name;
        $_formElement->options[ 'label' ] = !empty( $conf->label )
            ? $conf->label : ucfirst( $this->getName() );
        $_where  = [ 'status_id' => [ Status::NEW_, Status::NORMAL ] ];
        $_order                           = $conf->fields;
        $_fields                          = array_keys( $conf->fields );
        $_mask = null;
        if (!empty( $conf->query ) && strlen( $conf->query )) {
            $query   =
                $this->getQueryServiceVerify()->get( $conf->query )
                     ->process();
            $_where  = $query->getWhere();
            $_order  = $query->getOrder();
            $_fields = $query->getFields();
            $_mask   = $query->getFormat( 'label' );
        }
        $_lAll    =
            $this->getGatewayServiceVerify()->get( $conf->model )
                 ->find( $_where, $_order );
        $_options = [ ];
        foreach ($_lAll as $_lRow) {
            $_lLabel = '';
            $_lvalue = $_lRow->id();

            if ($_mask !== null && strlen( $_mask )) {
                $_vals = [ ];
                foreach ($_fields as $field) {
                    $_vals[ $field ] = $_lRow->$field;
                }
                $_lLabel = vsprintf( $_mask, $_vals );
            } else {
                foreach ($_fields as $_k) {
                    if (strlen( $_lLabel )) {
                        $_lLabel .= '  [ ';
                        $_lLabel .= $_lRow->$_k;
                        $_lLabel .= ' ] ';
                    } else {
                        $_lLabel .= $_lRow->$_k;
                    }
                }
            }
            $_options[ $_lvalue ] = $_lLabel;
        }
        if ( !empty($conf->default) && isset($_options[$conf->default])) {
            $options = [ $conf->default => $_options[$conf->default] ];
            unset ($_options[$conf->default]);
            $options += $_options;
            $_formElement->options['value_options'] = $options;
//                $_formElement->attributes[ 'value' ]      = $conf[ 'default' ];
        } else {
            $_formElement->options['value_options'] += $_options;
        }
//        $_formElement->options[ 'value_options' ] += $_options;
        $_formElement->attributes[ 'name' ] = $name;
        if (!empty( $conf->required )) {
            $_inputFilter->required = true;
            $_formElement->attributes[ 'required' ] = 'required';
            if (!empty( $_formElement->options[ 'label_attributes' ][ 'class' ] )
                &&
                strlen( $_formElement->options[ 'label_attributes' ][ 'class' ] )
            ) {
                $_formElement->options[ 'label_attributes' ][ 'class' ] .= ' required';
            } else {
                $_formElement->options[ 'label_attributes' ]
                    = [ 'class' => 'required' ];
            }
        }
        $result = [
            'filters'  => [ $name => $_inputFilter ],
            'elements' => [ $name => $_formElement ]
        ];

        return $result;
    }

}
