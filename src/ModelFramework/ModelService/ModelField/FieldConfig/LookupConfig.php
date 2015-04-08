<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 18:58
 */

namespace ModelFramework\ModelService\ModelField\FieldConfig;

class LookupConfig implements FieldConfigInterface
{

    public $type = '';
    public $group = '';
    public $max = 0;
    public $required = 0;
    public $label = '';
    public $model = '';
    public $query = '';
    public $fields = [];
    public $default = 0;

    /**
     * @param array $a
     *
     * @return array
     */
    public function exchangeArray(array $a)
    {
        $this->type     = (isset($a['type'])) ? $a['type'] : '';
        $this->group    = (isset($a['group'])) ? $a['group'] : '';
        $this->max      = (isset($a['max'])) ? $a['max'] : 0;
        $this->required = (isset($a['required'])) ? $a['required'] : 0;
        $this->label    = (isset($a['label'])) ? $a['label'] : '';

        $this->model   = (isset($a['model'])) ? $a['model'] : '';
        $this->query   = (isset($a['query'])) ? $a['query'] : '';
        $this->fields  = (isset($a['fields'])) ? $a['fields'] : [];
        $this->default = (isset($a['default'])) ? $a['default'] : '';

    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type'     => $this->type,
            'group'    => $this->group,
            'max'      => $this->max,
            'required' => $this->required,
            'label'    => $this->label,
            'model'    => $this->model,
            'query'    => $this->query,
            'fields'   => $this->fields,
            'default'  => $this->default,
        ];
    }
}
