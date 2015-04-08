<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 18:58
 */

namespace ModelFramework\ModelService\ModelConfig;

class ParsedModelConfig
{

    public $fields = [];
    public $joins = [];
    public $adapter = '';
    public $model = '';
    public $label = '';
    public $table = '';
    public $fieldsets = [];
    public $unique = [];
    public $labels = [];

    /**
     * @param array $a
     *
     * @return array
     */
    public function exchangeArray(array $a)
    {
        $this->fields    = (isset($a['fields'])) ? $a['fields'] : [];
        $this->joins     = (isset($a['joins'])) ? $a['joins'] : [];
        $this->adapter   = (isset($a['adapter'])) ? $a['adapter'] : '';
        $this->model     = (isset($a['model'])) ? $a['model'] : '';
        $this->label     = (isset($a['label'])) ? $a['label'] : '';
        $this->table     = (isset($a['table'])) ? $a['table'] : '';
        $this->fieldsets = (isset($a['fieldsets'])) ? $a['fieldsets'] : [];
        $this->unique    = (isset($a['unique'])) ? $a['unique'] : [];
        $this->labels    = (isset($a['labels'])) ? $a['labels'] : [];

    }


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'fields'    => $this->fields,
            'joins'     => $this->joins,
            'adapter'   => $this->adapter,
            'model'     => $this->model,
            'label'     => $this->label,
            'table'     => $this->table,
            'fieldsets' => $this->fieldsets,
            'unique'    => $this->unique,
            'labels'    => $this->labels,
        ];
    }

}
