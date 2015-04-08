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
use ModelFramework\FormService\StaticDataConfig\StaticDataConfig;
use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfigInterface;

class StaticLookupStrategy extends AbstractFormFieldStrategy
{

    public function s(
        FieldConfigInterface $conf,
        FormElementConfigInterface $_formElement,
        InputFilterConfigInterface $_inputFilter
    ) {
        $name = $this->getName() . '_id';
        if (!$this->isAllowed( $name ) || !$this->isNotLimited( $name )) {
            return [
                'elements' => [ ],
                'filters'  => [ ]
            ];
        }
        $_inputFilter->name               = $name;
        $_formElement->attributes[ 'class' ]      = 'static-select2';
        $_formElement->options[ 'label' ] = !empty( $conf->label )
            ? $conf->label : ucfirst( $this->getName() );

        $_lAll = $this->getConfigService()
                      ->get( 'StaticDataSource', $conf->model,
                          new StaticDataConfig() );
        if ($_lAll == null) {
            $_lAll = new StaticDataConfig();
        }
        $_options = [ ];
        foreach ($_lAll->options as $_key => $_lRow) {
            $_lLabel = $_lRow[ $_lAll->attributes[ 'select_field' ] ];
            $_lvalue = $_key;

            $_options[ $_lvalue ] = $_lLabel;
        }
        if (!empty( $conf->default ) && isset( $_options[ $conf->default ] )) {
            $options = [ $conf->default => $_options[ $conf->default ] ];
            unset ( $_options[ $conf->default ] );
            $options += $_options;
            $_formElement->options[ 'value_options' ] = $options;
//                $_formElement->attributes[ 'value' ]      = $conf[ 'default' ];
        } else {
            $_formElement->options[ 'value_options' ] = $_options;
        }
        $_formElement->options[ 'label' ]
            = $conf->fields[ $_lAll->attributes[ 'select_field' ] ];
        /* } */

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
