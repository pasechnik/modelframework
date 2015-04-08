<?php

namespace ModelFramework\FormService\FormConfig;

use ModelFramework\DataModel\DataModel;

/**
 * Class ModelConfig
 *
 * @package ModelFramework\ModelService\ModelConfig
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class FormConfig extends DataModel
{

    public $_model = 'FormConfig';
    public $_label = 'Model Config';
    public $_adapter = 'wepo_company';

    public $_fields
        = [
            '_id'       => [
                'type'     => 'pk',
                'datatype' => 'string',
                'default'  => ''
            ],
            'key'       => [
                'type'     => 'field',
                'datatype' => 'string',
                'default'  => ''
            ],
            'label'     => [
                'type'     => 'field',
                'datatype' => 'string',
                'default'  => ''
            ],
            'adapter'   => [
                'type'     => 'field',
                'datatype' => 'string',
                'default'  => ''
            ],
            'model'     => [
                'type'     => 'field',
                'datatype' => 'string',
                'default'  => ''
            ],
            'table'     => [
                'type'     => 'field',
                'datatype' => 'string',
                'default'  => ''
            ],
            'config'     => [
                'type'     => 'field',
                'datatype' => 'string',
                'default'  => 'custom'
            ],
            'fields'    => [
                'type'     => 'field',
                'datatype' => 'array',
                'default'  => []
            ],
            'groups'    => [
                'type'     => 'field',
                'datatype' => 'array',
                'default'  => []
            ],
            'unique'    => [
                'type'     => 'field',
                'datatype' => 'array',
                'default'  => []
            ],
            'observers' => [
                'type'     => 'field',
                'datatype' => 'array',
                'default'  => [],
            ],
        ];
    protected $_joins = [];
    public $_unique = ['model'];

    public $_id = '';
    public $config = 'custom';
    public $label = '';
    public $adapter = '';
    public $model = '';
    public $table = '';
    public $fields = [];
    public $groups = [];
    public $unique = [];
    public $observers = [];
}
