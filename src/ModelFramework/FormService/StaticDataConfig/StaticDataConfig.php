<?php

namespace ModelFramework\FormService\StaticDataConfig;

use ModelFramework\DataModel\DataModel;

/**
 * Class LogicConfig
 * @package ModelFramework\LogicService\LogicConfig
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class StaticDataConfig extends DataModel
{
    public $_model = 'StaticDataConfig';
    public $_label = 'Source data for static dropdowns';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'        => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'key'        => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'attributes' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'fields'     => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'options'    => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
    ];

    public $_id = '';
//    public $key = '';
//    public $attributes = [ ];
//    public $fields = [ ];
//    public $options = [ ];
}
