<?php
/**
 * Class DataMappingConfig
 * @package ModelFramework\DataMapping\DataMappingConfig
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataMapping\DataMappingConfig;

use ModelFramework\DataModel\DataModel;

class DataMappingConfig extends DataModel
{
    public $_model = 'DataMappingConfig';
    public $_label = 'Data Mapping';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'     => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'key'     => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'model'   => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'targets' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'post'    => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
    ];
    protected $_joins = [ ];
    public $_unique = [ 'model' ];

    public $_id = '';
    public $key = '';
    public $model = '';
    public $targets = [ ];
    public $post = [ ];
}
