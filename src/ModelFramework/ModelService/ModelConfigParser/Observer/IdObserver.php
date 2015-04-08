<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\ModelService\ModelConfigParser\Observer;

use ModelFramework\ModelService\ModelConfigParser\ModelConfigParser;

class IdObserver implements \SplObserver
{

    public function update(\SplSubject $subject)
    {
        /** @var ModelConfigParser $subject */
        $config = [];
        // add primary key _id

        $config['fields'] = [
            '_id' => [
                'type'      => 'pk',
                'fieldtype' => '_id',
                'datatype'  => 'string',
                'default'   => '',
                'label'     => 'ID',
                'source'    => '_id',
            ]
        ];

        $subject->addParsedConfig($config);

    }

}
