<?php
/**
 * Class ViewConfig
 * @package ModelFramework\ViewService\ViewConfig
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\ViewConfig;

use ModelFramework\DataModel\DataModel;

class ViewConfig extends DataModel
{
    public $_model = 'ViewConfig';
    public $_label = 'Model View Config';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'       => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'observers' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'document'  => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'title'     => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'custom'    => [ 'type' => 'field', 'datatype' => 'integer', 'default' => 0 ],
        'key'       => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'mode'      => [ 'type' => 'field', 'datatype' => 'string', 'default' => 'list' ],
        'model'     => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'template'  => [ 'type' => 'field', 'datatype' => 'string', 'default' => 'common/index.twig' ],
        'query'     => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'fields'    => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'actions'   => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'links'     => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'params'    => [
            'type'    => 'field', 'datatype' => 'array',
            'default' => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
        ],
        'order'     => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'groups'    => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'rows'      => [ 'type' => 'field', 'datatype' => 'integer', 'default' => 10 ],
        'limit'     => [ 'type' => 'field', 'datatype' => 'integer', 'default' => 10 ],

    ];
    protected $_joins = [ ];
    public $_unique = [ 'model' ];

    public $_id = '';
    public $observers = [ ];
    public $document = '';
    public $title = '';
    public $custom = 0;
    public $mode = '';
    public $key = '';
    public $model = '';
    public $template = 'common/index.twig';
    public $query = [ ];
    public $fields = [ ];
    public $actions = [ ];
    public $links = [ ];
    public $params = [ ];
    public $order = [ ];
    public $groups = [ ];
    public $rows = 10;
    public $limit = 10;
}
