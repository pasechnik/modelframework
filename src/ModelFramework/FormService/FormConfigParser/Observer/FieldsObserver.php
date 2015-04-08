<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\FormService\FormConfigParser\Observer;

use ModelFramework\FormService\FormConfigParser\FormConfigParser;
use ModelFramework\FormService\FormField\FormField;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class FieldsObserver
    implements \SplObserver, SubjectAwareInterface
{

    use SubjectAwareTrait;

    public function update( \SplSubject $subject )
    {
        /** @var FormConfigParser $subject */
        $this->setSubject( $subject );
        $modelConfig      = $subject->getModelConfigVerify();
        $parsedFormConfig = $subject->getParsedFormConfig();
        foreach ($modelConfig->fields as $field_name => $field_conf) {
            $field = $this->createField( $field_name, $field_conf );
            foreach ($field[ 'elements' ] as $key => $element) {
                if (isset( $parsedFormConfig->fieldsets_configs[ $field_conf[ 'group' ] ] )) {
                    $parsedFormConfig->fieldsets_configs[ $field_conf[ 'group' ] ]->elements[ ] =
                        $element;
                }
                $parsedFormConfig->validationGroup[ $field_conf[ 'group' ] ][ ] =
                    $key;
            }
            foreach ($field[ 'filters' ] as $key => $filter) {
                if (isset( $parsedFormConfig->fieldsets_configs[ $field_conf[ 'group' ] ] )) {
                    $parsedFormConfig->filters[ $field_conf[ 'group' ] ][ $key ] =
                        $filter;
                }
            }
        }
        $subject->setParsedFormConfig( $parsedFormConfig );
    }

    protected function createField( $name, $config )
    {
        /** @var FormConfigParser $subject */
        $subject   = $this->getSubject();
        $formField = new FormField();
        $formField
            ->chooseStrategy( $config[ 'type' ] )
            ->setFieldTypesService( $subject->getFieldTypesServiceVerify() )
            ->setQueryService( $subject->getQueryServiceVerify() )
            ->setGatewayService( $subject->getGatewayServiceVerify() )
            ->setGatewayService( $subject->getGatewayServiceVerify() )
            ->setConfigService( $subject->getConfigServiceVerify() )
            ->setDataModel( $subject->getDataModel() )
            ->setAclConfig( $subject->getAclConfigVerify() )
            ->setLimitFields( $subject->getLimitFields() )
            ->setName( $name )
            ->setFieldConfig( $config )
            ->init()
            ->parse();

        return $formField->getParsedFieldConfig();
    }

}
