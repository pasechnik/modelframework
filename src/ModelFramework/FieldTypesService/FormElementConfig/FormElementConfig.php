<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 18:58
 */

namespace ModelFramework\FieldTypesService\FormElementConfig;

class FormElementConfig implements FormElementConfigInterface
{

    public $type = '';
    public $name = '';
    public $attributes = [ ];
    public $options = [ ];

    /**
     * @param array $a
     *
     * @return array
     */
    public function exchangeArray( array $a )
    {
        $this->type       = ( isset( $a[ 'type' ] ) ) ? $a[ 'type' ] : '';
        $this->name       = ( isset( $a[ 'name' ] ) ) ? $a[ 'name' ] : '';
        $this->attributes =
            ( isset( $a[ 'attributes' ] ) ) ? $a[ 'attributes' ] : [ ];
        $this->options    =
            ( isset( $a[ 'options' ] ) ) ? $a[ 'options' ] : [ ];
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type'       => $this->type,
            'name'       => $this->name,
            'attributes' => $this->attributes,
            'options'    => $this->options,
        ];
    }

}
