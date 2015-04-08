<?php

namespace ModelFramework\AclService\AclConfig;

use ModelFramework\DataModel\DataModel;

/**
 * Class LogicConfig
 * @package ModelFramework\LogicService\LogicConfig
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class AclConfig extends DataModel
{

    public $_model = 'AclConfig';
    public $_label = 'Acl Config Data';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'    => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'key'    => [
            'type'     => 'field',
            'datatype' => 'string',
            'default'  => ''
        ],
        "model"  => [
            'type'     => 'field',
            'datatype' => 'string',
            'default'  => ''
        ],
        "role"   => [
            'type'     => 'field',
            'datatype' => 'string',
            'default'  => ''
        ],
        "type"   => [
            'type'     => 'field',
            'datatype' => 'string',
            'default'  => ''
        ],
        "data"   => [
            'type'     => 'field',
            'datatype' => 'array',
            'default'  => [ ]
        ],
        "modes"  => [
            'type'     => 'field',
            'datatype' => 'array',
            'default'  => [ ]
        ],
        "fields" => [
            'type'     => 'field',
            'datatype' => 'array',
            'default'  => [ ]
        ],
    ];

    public $_id = '';
    public $key = '';
    public $model = '';
    public $role = '';
    public $type = '';
    public $data = [ ];
    public $modes = [ ];
    public $fields = [ ];
}
