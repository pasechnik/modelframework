<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\ModelService\ModelConfigParser\Observer;

use ModelFramework\ModelService\ModelConfigParser\ModelConfigParser;

class GroupsObserver implements \SplObserver
{

    public function update(\SplSubject $subject)
    {
        /** @var ModelConfigParser $subject */

        $modelConfig = $subject->getModelConfig();

        $config = [];
        // process groups
        foreach ($modelConfig->groups as $_grp => $_fls) {
            if (is_numeric($_grp)) {
                $_grp = $_fls;
                $_baseFieldSet = $_grp == 'fields';
                $_fls = [
                    'label' => $modelConfig->model . ' information'
                ];
            } else {
                $_baseFieldSet
                    = isset($_fls ['base']) && $_fls ['base'] == true;
            }
            $_fls['elements'] = [];
            $_fls['base']     = [$_baseFieldSet];

            $config ['fieldsets'] [$_grp] = $_fls;
        }

        $subject->addParsedConfig($config);

    }

}
