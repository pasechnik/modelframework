<?php
/**
 * Class ModelList
 *
 * @package ModelFramework\ModelService\ModelList
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelService\ModelList;

use ModelFramework\DataModel\DataModel;

class ModelList extends DataModel
{

    public $_model = 'modellist';
    public $_label = 'Model List';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'   => [
            'type'     => 'pk',
            'datatype' => 'string',
            'default'  => ''
        ],
        'key'   => [
            'type'     => 'field',
            'datatype' => 'string',
            'default'  => ''
        ],
        'label' => [
            'type'     => 'field',
            'datatype' => 'string',
            'default'  => ''
        ],
        'model' => [
            'type'     => 'field',
            'datatype' => 'string',
            'default'  => ''
        ],
    ];

}
