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

class ButtonObserver implements \SplObserver, ParsedFormConfigAwareInterface
{

    use ParsedFormConfigAwareTrait;

    public function update( \SplSubject $subject )
    {
        /** @var FormConfigParser $subject */
        $FSConfig             = $this->setParsedFormConfig()
                                     ->getParsedFormConfigVerify();
        $FSConfig->name       = 'ButtonFieldset';
        $FSConfig->group      = 'button';
        $FSConfig->type       = 'fieldset';
        $FSConfig->options    = [ ];
        $FSConfig->attributes = [ 'name' => 'button', 'class' => 'buttons' ];
        $FSConfig->fieldsets  = [ ];
        $FSConfig->elements   = [
            'submit' => [
                'type'       => 'Zend\Form\Element',
                'attributes' => [
                    'value' => 'Save',
                    'name'  => 'submit',
                    'type'  => 'submit',
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
