<?php
/**
 * Class QueryConfig
 * @package ModelFramework\QueryService\QueryConfig
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\QueryConfig;

use ModelFramework\DataModel\DataModel;

class QueryConfig extends DataModel
{
    public $_model = 'QueryConfig';
    public $_label = 'Query Config';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'       => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'key'       => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'model'     => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'observers' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'fields'    => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'actions'   => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'links'     => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'order'     => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],

    ];

    public $_unique = [ 'key' ];

    public $_id = '';
    public $key = '';
    public $model = '';
    public $observers = [ ];
    public $fields = [ ];
    public $actions = [ ];
    public $links = [ ];
    public $order = [ ];
}
