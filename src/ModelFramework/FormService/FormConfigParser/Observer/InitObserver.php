<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\FormService\FormConfigParser\Observer;

use ModelFramework\FormService\FormConfigParser\FormConfigParser;

class InitObserver implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        /** @var FormConfigParser $subject */
        $modelConfig = $subject->getModelConfigVerify();
        $formConfig  = [
            'name'            => $modelConfig->model . 'Form',
            'group'           => 'form',
            'type'            => 'form',
            'options'         => [
                'label' => $modelConfig->model . ' information',
            ],
            'attributes'      => [
                'class'      => 'validate apiform',
                'method'     => 'post',
                'name'       => $modelConfig->model . 'form',
                'data-scope' => strtolower( $modelConfig->model ),
                'data-id'    => $subject->getDataModel()->id(),
            ],
            // , 'action' => 'reg'
            'fieldsets'       => [ ],
            'elements'        => [ ],
            'filters'         => [ ],
            'validationGroup' => [ ],
        ];

        $subject->addParsedConfig( $formConfig );
    }
}
