<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\FormService\FormConfigParser\Observer;

use ModelFramework\FormService\FormConfig\ParsedFormConfig;
use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareInterface;
use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareTrait;
use ModelFramework\FormService\FormConfigParser\FormConfigParser;

class GroupsObserver implements \SplObserver, ParsedFormConfigAwareInterface
{

    use ParsedFormConfigAwareTrait;

    public function update(\SplSubject $subject)
    {
        /** @var FormConfigParser $subject */
        $modelConfig = $subject->getModelConfigVerify();
        $config = [];
        // process groups
        foreach ($modelConfig->groups as $_grp => $_fls) {
            $parsedFSConfig = $this->setParsedFormConfig()
                ->getParsedFormConfigVerify();

            if (is_numeric($_grp)) {
                $_grp          = $_fls;
                $_baseFieldSet = $_grp == 'fields';
                $_label        = $modelConfig->model;
            } else {
                $_baseFieldSet
                    = isset($_fls ['base']) && $_fls ['base'] == true;
                if ( !isset($_fls['label'])) {
                    $_label = $modelConfig->model;
                } else {
                    $_label = $_fls['label'];
                }
            }
            $parsedFSConfig->name       = $modelConfig->model . 'Fieldset';
            $parsedFSConfig->group      = $_grp;
            $parsedFSConfig->type       = 'fieldset';
            $parsedFSConfig->options    = ['label' => $_label];
            $parsedFSConfig->attributes = [
                'name'  => $_grp,
                'class' => 'table'
            ];
            $config ['fieldsets'] [$_grp] = [
                'type'    => $modelConfig->model . 'Fieldset',
                'options' => ['label' => $_label],
            ];
            if ($_baseFieldSet == true) {
                $config['fieldsets'][$_grp]['options']['use_as_base_fieldset']
                    = true;
            }
            $config['fieldsets_configs'][$_grp] = $parsedFSConfig;
        }

        $subject->addParsedConfig($config);

    }

}
