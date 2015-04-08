<?php

namespace ModelFramework\FormService;

use ModelFramework\FieldTypesService\InputFilterConfig\InputFilterConfigInterface;
use ModelFramework\FormService\FormConfig\ParsedFormConfig;
use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareInterface;
use SebastianBergmann\Exporter\Exception;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Input;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;

class DataForm extends Form
    implements ParsedFormConfigAwareInterface
{

    private $_parsedFormConfig = null;
    private $_route = null;
    private $_action = null;
    private $_backAction = 'list';
    private $_actionParams = null;
    protected $_name = '';


    /**
     * @param ParsedFormConfig $parsedFormConfig
     *
     * @return $this
     */
    public function setParsedFormConfig(
        ParsedFormConfig $parsedFormConfig = null
    ) {
        if ($parsedFormConfig === null) {
            $parsedFormConfig = new ParsedFormConfig();
        }
        $this->_parsedFormConfig = $parsedFormConfig;
        $this->parseConfig( $parsedFormConfig );
        return $this;
    }

    /**
     * @return ParsedFormConfig
     */
    public function getParsedFormConfig()
    {
        return $this->_parsedFormConfig;
    }

    /**
     * @return ParsedFormConfig
     * @throws \Exception
     */
    public function getParsedFormConfigVerify()
    {
        $parsedFormConfig = $this->getParsedFormConfig();
        if ($parsedFormConfig == null
            || !$parsedFormConfig instanceof ParsedFormConfig
        ) {
            throw new \Exception( 'ParsedFormConfig is not set in '
                                  . get_class( $this ) );
        }

        return $this->getParsedFormConfig();
    }

    public function bind( $object, $flags = FormInterface::VALUES_NORMALIZED )
    {
        foreach ($this->getFieldsets() as $fieldset) {
            $fieldset->setObject( $object );
        }
        $result = parent::bind( $object, $flags );

        return $result;
    }

    /**
     * Recursively extract values for elements and sub-fieldsets, and populate form values
     *
     * @return array
     */
    public function extract()
    {
        $values = parent::extract();
        foreach ($this->getFieldsets() as $fieldset) {
            if (isset( $fieldset->getOptions()[ 'use_as_base_fieldset' ] )) {
                continue;
            }
            $name            = $fieldset->getName();
            $values[ $name ] = $fieldset->extract();
            $fieldset->populateValues( $values[ $name ] );
        }

        return $values;
    }

    public function parseConfig( ParsedFormConfig $config )
    {
        $this->_name = $config->name;
        $this->setName( $config->group );
        $this->setValidationGroup( $config->validationGroup );
        $this->setOptions( $config->options );
        foreach ($config->attributes as $_k => $_v) {
            $this->setAttribute( $_k, $_v );
        }
        foreach ($config->fieldsets as $_k => $_v) {
            if (!isset( $config->fieldsets_configs[ $_k ] )) {
                throw new \Exception( "Config for $_k fieldset is not set in $config->name ParsedFormConfig" );
            }
            $parsedFSConfig = $config->fieldsets_configs[ $_k ];
//            $cf       = new ParsedFormConfig();
            $fieldset = new DataFieldset();
            $fieldset->setParsedFormConfig( $parsedFSConfig );
//            $fieldset->parseconfig($cf->exchangeArray($fc));
            if (!empty( $_v[ 'options' ] )) {
                $fieldset->setOptions( $_v[ 'options' ] );
            }
            $this->add( $fieldset );
        }
        foreach ($config->elements as $_k => $_v) {
            if (is_array( $_v )) {
                $this->add( $_v );
            } elseif ($_v instanceof FormElementConfigInterface) {
                $this->add( $_v->toArray() );
            }
        }

        $this->setInputFilter( $this->createInputFilter( $config ) );

        return $this;
    }

    protected function createInputFilter( ParsedFormConfig $config )
    {
        $inputFilter = new InputFilter();
        $factory     = new Factory();
        foreach ($config->validationGroup as $_group => $_fields) {
            if (is_array( $_fields )) {
                $fieldsetFilter = new InputFilter();
                foreach ($_fields as $_fName) {
                    if (isset( $config->filters[ $_group ][ $_fName ] )) {
                        $filterConfig = $config->filters[ $_group ][ $_fName ];
                        if (is_array( $filterConfig )) {
                            $fieldsetFilter->add(
                                $factory->createInput( $filterConfig ) );
                        } elseif ($filterConfig instanceof
                                  InputFilterConfigInterface
                        ) {
                            $fieldsetFilter->add(
                                $factory->createInput( $filterConfig->toArray() ) );
                        }
                    }
                }
                $inputFilter->add( $fieldsetFilter, $_group );
            } else {
                if (isset( $config->filters[ $_fields ] )) {
                    $inputFilter->add( $factory->createInput( $config->filters[ $_fields ] ) );
                }
            }
        }

        return $inputFilter;
    }

    public function addInputFilter(
        InputFilterInterface $addInputFilter,
        $fieldsetName = null
    ) {
        $inputFilter = $this->getInputFilter();

        $_inputs = $addInputFilter->getInputs();
        foreach ($this->getValidationGroup() as $_group => $_fields) {
            if ($fieldsetName !== null && $_group !== $fieldsetName) {
                continue;
            }
            if (is_array( $_fields )) {
                $fieldsetFilter = new InputFilter();
                foreach ($_fields as $_fName) {
                    if (isset( $_inputs[ $_fName ] )) {
                        $fieldsetFilter->add( $_inputs[ $_fName ] );
                    }
                }
                $inputFilter->add( $fieldsetFilter, $_group );
            } else {
                if (isset( $_inputs[ $_fields ] )) {
                    $inputFilter->add( $_inputs[ $_fields ] );
                }
            }
        }

//        if ( !in_array( $fieldsetName, array_keys( $inputFilter->getInputs() ) ) )
//        {
//            throw new \Exception( "Sorry, $fieldsetName is invalid fieldset name", null, null );
//        }
//        $inputFilter->add( $addInputFilter, $fieldsetName );
        $this->setInputFilter( $inputFilter );
    }

    public function updateInputFilter(
        InputFilterInterface $addInputFilter,
        $fieldsetName
    ) {
        $inputFilter = $this->getInputFilter();

        if (!in_array( $fieldsetName, array_keys( $inputFilter->getInputs() ) )
        ) {
            throw new \Exception( "Sorry, $fieldsetName is invalid fieldset name",
                null, null );
        }
        foreach ($addInputFilter->getValues() as $key => $value) {
            $this->addInput( $addInputFilter->get( $key ), $fieldsetName );
        }
    }

    public function addInput( Input $input, $fieldsetName )
    {
        $inputFilter = $this->getInputFilter();
        if (!in_array( $fieldsetName, array_keys( $inputFilter->getInputs() ) )
        ) {
            throw new \Exception( "Sorry, $fieldsetName is invalid fieldset name",
                null, null );
        }
        $fieldsetFilter = $inputFilter->get( $fieldsetName );
        $fieldsetFilter->add( $input );

        $inputFilter->add( $fieldsetFilter, $fieldsetName );
        $this->setInputFilter( $inputFilter );
    }

    public function addValidationField( $ValidationGroupName, $field )
    {
        $_groups = $this->getValidationGroup();
        if (is_array( $field )) {
            foreach ($field as $_k => $_v) {
                $_groups[ $ValidationGroupName ][ $_k ] = $_v;
            }
        } else {
            $_groups[ $ValidationGroupName ][ ] = $field;
        }
        $this->setValidationGroup( $_groups );

        return $this;
    }

    public function newValidationField( $field )
    {
        $_groups = $this->getValidationGroup();
        if (is_array( $field )) {
            foreach ($field as $_k => $_v) {
                $_groups[ $_k ] = $_v;
            }
        } else {
            $_groups[ ] = $field;
        }
        $this->setValidationGroup( $_groups );

        return $this;
    }

    public function getConfig()
    {
        $wrs    = preg_split( '/\\\/', get_class( $this ), -1,
            PREG_SPLIT_NO_EMPTY );
        $result = [
            'name'            => empty( $this->_name ) ? array_pop( $wrs )
                : $this->_name,
            'group'           => $this->getName(),
            'type'            => 'form',
            'options'         => $this->getOptions(),
            'attributes'      => $this->getAttributes(),
            'fieldsets'       => [ ],
            'elements'        => [ ],
            'validationGroup' => $this->getValidationGroup(),
        ];
        $label  = $this->getLabel();
        if (!empty( $label )) {
            $result[ 'options' ][ 'label' ] = $this->getLabel();
        }
        foreach ($this->getFieldsets() as $_k => $_fieldset) {
            $wrs                                       = preg_split( '/\\\/',
                get_class( $_fieldset ), -1, PREG_SPLIT_NO_EMPTY );
            $result[ 'fieldsets' ][ $_k ][ 'type' ]    = array_pop( $wrs );
            $result[ 'fieldsets' ][ $_k ][ 'options' ] =
                $_fieldset->getOptions();
        }
        foreach ($this->getElements() as $_k => $_element) {
            $result[ 'elements' ][ $_k ][ 'type' ]       =
                get_class( $_element );
            $result[ 'elements' ][ $_k ][ 'attributes' ] =
                $_element->getAttributes();
            $result[ 'elements' ][ $_k ][ 'options' ]    =
                $_element->getOptions();
        }

        return $result;
    }

    public function setRoute( $route )
    {
        $this->_route = $route;

        return $this;
    }

    public function setAction( $action )
    {
        $this->_action = $action;

        return $this;
    }

    public function setActionParams( $actionParams )
    {
        $this->_actionParams = $actionParams;

        return $this;
    }

    public function setBackAction( $action )
    {
        $this->_backAction = $action;

        return $this;
    }

    public function getRoute()
    {
        return $this->_route;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function getActionParams( $paramName = null )
    {
        return isset( $paramName )
               && array_key_exists( $paramName, $this->_actionParams ) ?
            $this->_actionParams[ $paramName ] : $this->_actionParams;
    }

    public function getBackAction()
    {
        return $this->_backAction;
    }
}
