<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 18:58
 */

namespace ModelFramework\FieldTypesService\FieldType;

class FieldType implements FieldTypeInterface
{

    public $type = '';
    public $fieldtype = '';
    public $datatype = '';
    public $default = 0;
    public $label = '';
    public $group = '';
//    public $source = '';

    /**
     * @param array $a
     *
     * @return array
     */
    public function exchangeArray(array $a)
    {
        $this->type      = (isset($a['type'])) ? $a['type'] : '';
        $this->fieldtype = (isset($a['fieldtype'])) ? $a['fieldtype'] : '';
        $this->datatype  = (isset($a['datatype'])) ? $a['datatype'] : '';
        $this->default   = (isset($a['default'])) ? $a['default'] : '';
        $this->label     = (isset($a['label'])) ? $a['label'] : '';
        $this->group     = (isset($a['group'])) ? $a['group'] : '';
//      $this->source  = (isset($a['source'])) ? $a['source'] : '';
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type'      => $this->type,
            'fieldtype' => $this->fieldtype,
            'datatype'  => $this->datatype,
            'default'   => $this->default,
            'label'     => $this->label,
            'group'     => $this->group,
//          'source'    => $this->source,
        ];
    }

}
