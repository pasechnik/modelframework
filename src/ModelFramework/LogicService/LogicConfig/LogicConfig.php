<?php

namespace ModelFramework\LogicService\LogicConfig;

use ModelFramework\DataModel\DataModel;

/**
 * Class LogicConfig
 * @package ModelFramework\LogicService\LogicConfig
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class LogicConfig extends DataModel
{
    public $_model = 'LogicConfig';
    public $_label = 'Logic Config Data';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'       => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'key'       => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'model'     => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'observers' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
    ];

    public $_id = '';
    public $key = '';
    public $model = '';
    public $observers = [ ];
}
