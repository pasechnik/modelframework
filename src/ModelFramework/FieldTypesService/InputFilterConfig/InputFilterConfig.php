<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 18:58
 */

namespace ModelFramework\FieldTypesService\InputFilterConfig;

class InputFilterConfig implements InputFilterConfigInterface
{

    public $name = '';
    public $required = false;
    public $filters = [ ];
    public $validators = [ ];

    /**
     * @param array $a
     *
     * @return array
     */
    public function exchangeArray( array $a )
    {
        $this->name       = ( isset( $a[ 'name' ] ) ) ? $a[ 'name' ] : '';
        $this->required   =
            ( isset( $a[ 'required' ] ) ) ? $a[ 'required' ] : false;
        $this->filters    =
            ( isset( $a[ 'filters' ] ) ) ? $a[ 'filters' ] : [ ];
        $this->validators =
            ( isset( $a[ 'validators' ] ) ) ? $a[ 'validators' ] : [ ];
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name'       => $this->name,
            'required'   => $this->required,
            'filters'    => $this->filters,
            'validators' => $this->validators,
        ];
    }

}
