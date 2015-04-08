<?php

namespace ModelFramework\FormService;

use ModelFramework\FieldTypesService\FormElementConfig\FormElementConfigInterface;
use ModelFramework\FormService\FormConfig\ParsedFormConfig;
use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareInterface;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class DataFieldset extends Fieldset implements InputFilterProviderInterface, ParsedFormConfigAwareInterface
{
    private $_parsedFormConfig = null;
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
        $this->parseConfig($parsedFormConfig);
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
            throw new \Exception('ParsedFormConfig is not set in '
                . get_class($this));
        }

        return $this->getParsedFormConfig();
    }

    public function parseConfig(ParsedFormConfig $config)
    {
        $this->_name = $config->name;
        $this->setName($config->group);
        $this->setOptions($config->options);
        foreach ($config->attributes as $_k => $_v) {
            $this->setAttribute($_k, $_v);
        }
        foreach ($config->fieldsets as $_k => $_v) {
            if (!isset($config->fieldsets_configs[ $_k ])) {
                throw new \Exception("Config for $_k fieldset is not set in $config->name ConfigForm");
            }
            $parsedFSConfig = $config->fieldsets_configs[ $_k ];
            $fieldset = new DataFieldset();
            $fieldset->setParsedFormConfig( $parsedFSConfig );

            if (!empty($_v[ 'options' ])) {
                $fieldset->setOptions($_v[ 'options' ]);
            }
            $this->add($fieldset);
        }
        foreach ($config->elements as $_k => $_v) {
            if(is_array($_v)){
                $this->add($_v);
            }
            elseif($_v instanceof FormElementConfigInterface)
            {
                $this->add($_v->toArray());
            }
        }

        return $this;
    }

    public function getConfig()
    {
        $wrs    = preg_split('/\\\/', get_class($this), -1, PREG_SPLIT_NO_EMPTY);
        $result = [
            'name'       => empty($this->_name) ? array_pop($wrs) : $this->_name,
            'group'      => $this->getName(),
            'type'       => 'fieldset',
            'options'    => $this->getOptions(),
            'attributes' => $this->getAttributes(),
            'fieldsets'  => [ ],
            'elements'   => [ ],
        ];
        $label  = $this->getLabel();
        if (!empty($label)) {
            $result[ 'options' ][ 'label' ] = $this->getLabel();
        }
        foreach ($this->getFieldsets() as $_k => $_fieldset) {
            $wrs                                       =
                preg_split('/\\\/', get_class($_fieldset), -1, PREG_SPLIT_NO_EMPTY);
            $result[ 'fieldsets' ][ $_k ][ 'type' ]    = array_pop($wrs);
            $result[ 'fieldsets' ][ $_k ][ 'options' ] = $_fieldset->getOptions();
        }
        foreach ($this->getElements() as $_k => $_element) {
            $result[ 'elements' ][ $_k ][ 'type' ]       = get_class($_element);
            $result[ 'elements' ][ $_k ][ 'attributes' ] = $_element->getAttributes();
            $result[ 'elements' ][ $_k ][ 'options' ]    = $_element->getOptions();
        }

        return $result;
    }

    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => true,
            ],
        ];
    }
}
