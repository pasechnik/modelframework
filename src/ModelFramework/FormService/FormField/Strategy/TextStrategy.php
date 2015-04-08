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

class TextStrategy extends AbstractFormFieldStrategy
{

    public function s(
        FieldConfigInterface $conf,
        FormElementConfigInterface $_formElement,
        InputFilterConfigInterface $_inputFilter
    ) {
//        prn( 'TextStrategy ->s()', $conf, $_formElement, $_inputFilter );
        if (!$this->isAllowed( $this->getName() ) ||
            !$this->isNotLimited( $this->getName() )
        ) {
            return [
                'elements' => [ ],
                'filters'  => [ ]
            ];
        }
        $_inputFilter->name               = $this->getName();
        $_formElement->options[ 'label' ] = isset( $conf->label )
            ? $conf->label : ucfirst( $this->getName() );

        $_formElement->attributes[ 'name' ] = $this->getName();
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
        if (!empty( $conf->min )){
            foreach($_inputFilter->validators as $v => $validator){
                if($validator['name'] == 'StringLength')
                {
                    $_inputFilter->validators[$v]['options']['min'] = $conf->min;
                    break;
                }
            }
        }
        if (!empty( $conf->max )){
            foreach($_inputFilter->validators as $v => $validator){
                if($validator['name'] == 'StringLength')
                {
                    $_inputFilter->validators[$v]['options']['max'] = $conf->max;
                    break;
                }
            }
        }

        $result = [
            'filters'  => [ $this->getName() => $_inputFilter ],
            'elements' => [ $this->getName() => $_formElement ]
        ];

        return $result;
    }

}
