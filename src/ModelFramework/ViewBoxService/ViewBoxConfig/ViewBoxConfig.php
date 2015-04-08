<?php
/**
 * Class ViewBoxConfig
 * @package ModelFramework\ViewBoxConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewBoxService\ViewBoxConfig;

use ModelFramework\DataModel\DataModel;

class ViewBoxConfig extends DataModel
{
    public $_model = 'ViewBoxConfig';
    public $_label = 'ViewBox Config';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'      => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'document' => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'title'    => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'key'     => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'mode'     => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'template' => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'blocks'   => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
    ];

    protected $_joins = [ ];
    public $_unique = [ 'document' ];

    public $_id = '';
    public $document = '';
    public $title = '';
    public $key = '';
    public $blocks = [ ];
}
