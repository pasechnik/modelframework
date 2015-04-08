<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\FormService\FormConfigParser\Observer;

use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareInterface;
use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareTrait;
use ModelFramework\FormService\FormConfigParser\FormConfigParser;

class SaUrlObserver implements \SplObserver, ParsedFormConfigAwareInterface
{

    use ParsedFormConfigAwareTrait;

    public function update( \SplSubject $subject )
    {
        /** @var FormConfigParser $subject */
        $FSConfig             = $this->setParsedFormConfig()
                                     ->getParsedFormConfigVerify();
        $FSConfig->name       = 'SaUrlFieldset';
        $FSConfig->group      = 'saurl';
        $FSConfig->type       = 'fieldset';
        $FSConfig->options    = [ ];
        $FSConfig->attributes = [ 'name' => 'saurl' ];
        $FSConfig->fieldsets  = [ ];
        $FSConfig->elements   = [
            'back' => [
                'type'       => 'Zend\Form\Element',
                'attributes' => [
                    'name' => 'back',
                    'type' => 'hidden',
                ],
                'options'    => [ ],
            ],
        ];
        $config               = [ ];
        $config[ 'fieldsets' ][ $FSConfig->name ]
                                                          =
            [ 'type' => $FSConfig->name ];
        $config[ 'fieldsets_configs' ][ $FSConfig->name ] = $FSConfig;

        $subject->addParsedConfig( $config );
    }

}
