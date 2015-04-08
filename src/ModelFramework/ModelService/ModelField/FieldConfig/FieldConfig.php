<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 18:58
 */

namespace ModelFramework\ModelService\ModelField\FieldConfig;

class FieldConfig implements FieldConfigInterface
{

    public $type = '';
    public $group = '';
    public $min = 0;
    public $max = 0;
    public $required = 0;
    public $label = '';
    public $default = 0;
    public $model = '';
    public $query = '';
    public $fields = [ ];

    /**
     * @param array $a
     *
     * @return array
     */
    public function exchangeArray( array $a )
    {
        $this->type     = ( isset( $a[ 'type' ] ) ) ? $a[ 'type' ] : '';
        $this->group    = ( isset( $a[ 'group' ] ) ) ? $a[ 'group' ] : '';
        $this->min      = ( isset( $a[ 'min' ] ) ) ? $a[ 'min' ] : 0;
        $this->max      = ( isset( $a[ 'max' ] ) ) ? $a[ 'max' ] : 0;
        $this->required = ( isset( $a[ 'required' ] ) ) ? $a[ 'required' ] : 0;
        $this->label    = ( isset( $a[ 'label' ] ) ) ? $a[ 'label' ] : '';
        $this->default  = ( isset( $a[ 'default' ] ) ) ? $a[ 'default' ] : '';
        $this->model    = ( isset( $a[ 'model' ] ) ) ? $a[ 'model' ] : '';
        $this->query    = ( isset( $a[ 'query' ] ) ) ? $a[ 'query' ] : '';
        $this->fields   = ( isset( $a[ 'fields' ] ) ) ? $a[ 'fields' ] : [ ];
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type'     => $this->type,
            'group'    => $this->group,
            'min'      => $this->min,
            'max'      => $this->max,
            'required' => $this->required,
            'label'    => $this->label,
            'default'  => $this->default,
            'model'    => $this->model,
            'query'    => $this->query,
            'fields'   => $this->fields,
        ];
    }

}
