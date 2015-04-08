<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\ModelService\ModelConfigParser\Observer;

use ModelFramework\ModelService\ModelConfigParser\ModelConfigParser;

class InitObserver implements \SplObserver
{
    public function update(\SplSubject $subject) {
        /** @var ModelConfigParser $subject */

        $modelConfig = $subject->getModelConfig();

        // init
        $config = [
            'fields'    => [],
            'joins'     => [],
            //            'unique'       => [ ],
            'adapter'   => $modelConfig->adapter,
            'model'     => $modelConfig->model,
            'label'     => $modelConfig->label,
            'table'     => $modelConfig->table,
            'fieldsets' => [],
            'unique'    => $modelConfig->unique,
        ];

        $subject->addParsedConfig( $config );

    }
}
