<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 18:58
 */

namespace ModelFramework\FormService\FormConfig;

class ParsedFormConfig
{

    public $_id = '';
    public $name = '';
    public $group = '';
    public $type = '';
    public $options = [];
    public $attributes = [];
    public $fieldsets = [];
    public $fieldsets_configs = [];
    public $elements = [];
    public $filters = [];
    public $validationGroup = [];

    /**
     * @param array $a
     *
     * @return array
     */
    public function exchangeArray(array $a)
    {
        $this->_id               = (isset($a['_id'])) ? $a['_id'] : 0;
        $this->name              = (isset($a['name'])) ? $a['name'] : '';
        $this->group             = (isset($a['group'])) ? $a['group'] : '';
        $this->type              = (isset($a['type'])) ? $a['type'] : '';
        $this->options           = (isset($a['options'])) ? $a['options'] : [];
        $this->attributes        = (isset($a['attributes'])) ? $a['attributes']
            : [];
        $this->fieldsets         = (isset($a['fieldsets'])) ? $a['fieldsets']
            : [];
        $this->fieldsets_configs = (isset($a['fieldsets_configs']))
            ? $a['fieldsets_configs'] : [];
        $this->elements          = (isset($a['elements'])) ? $a['elements']
            : [];
        $this->filters           = (isset($a['filters'])) ? $a['filters'] : [];
        $this->validationGroup   = (isset($a['validationGroup']))
            ? $a['validationGroup'] : [];
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            '_id'               => $this->_id,
            'name'              => $this->name,
            'group'             => $this->group,
            'type'              => $this->type,
            'options'           => $this->options,
            'attributes'        => $this->attributes,
            'fieldsets'         => $this->fieldsets,
            'fieldsets_configs' => $this->fieldsets_configs,
            'elements'          => $this->elements,
            'filters'           => $this->filters,
            'validationGroup'   => $this->validationGroup,
        ];
    }

}
