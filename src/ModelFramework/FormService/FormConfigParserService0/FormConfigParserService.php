<?php
/**
 * Class FormConfigParserService
 *
 * @package ModelFramework\ModelConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService\FormConfigParserService;

use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelAwareInterface;
use ModelFramework\DataModel\DataModelAwareTrait;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FormService\StaticDataConfig\StaticDataConfig;
use ModelFramework\FormService\ConfigForm;
use ModelFramework\FormService\FormConfigParser\FormConfigParser;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelService\ModelConfig\ModelConfig;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use ModelFramework\Utility\SplSubject\SplSubjectTrait;
use Wepo\Model\Status;

class FormConfigParserService
    implements FormConfigParserServiceInterface, ConfigServiceAwareInterface
//    , FieldTypesServiceAwareInterface, GatewayServiceAwareInterface, QueryServiceAwareInterface
{

    use DataModelAwareTrait, ConfigServiceAwareTrait;
//    use FieldTypesServiceAwareTrait, GatewayServiceAwareTrait, QueryServiceAwareTrait;

    private $_limitFields = [];

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function limitFields(array $fields = [])
    {
        $this->_limitFields = $fields;
        return $this;
    }

    public function parse()
    {
        $modelName = $this->getDataModelVerify()->getModelName();
        $modelConfig = $this->getConfigServiceVerify()->getByObject($modelName, new ModelConfig());
        if ($modelConfig == null) {
            throw new \Exception('Please fill ModelConfig for the ' . $modelName
                . '. I can\'t work on');
        }

        $formConfigParser = new FormConfigParser();
//        $formConfigParser->setFieldTypesService($this->getFieldTypesServiceVerify());
//        $formConfigParser->setModelConfi($modelConfig);
        $formConfigParser->init()->notify();

        return $formConfigParser;

    }

    public function init(){

    }

    /**
     * @return array
     */
    public function getLimitFields()
    {
        return $this->_limitFields;
    }

    protected $_utilityFieldsetsConfigs
        = [
            'ButtonFieldset' => [
                'name'       => 'ButtonFieldset',
                'group'      => 'button',
                'type'       => 'fieldset',
                'options'    => [],
                'attributes' => [
                    'name'  => 'button',
                    'class' => 'buttons',
                ],
                'fieldsets'  => [],
                'elements'   => [
                    'submit' => [
                        'type'       => 'Zend\Form\Element',
                        'attributes' => [
                            'value' => 'Save',
                            'name'  => 'submit',
                            'type'  => 'submit',
                        ],
                        'options'    => [],
                    ],
                ],
            ],
            'SuUrlFieldset'  => [
                'name'       => 'SuUrlFieldset',
                'group'      => 'saurl',
                'type'       => 'fieldset',
                'options'    => [],
                'attributes' => [
                    'name' => 'saurl',
                ],
                'fieldsets'  => [],
                'elements'   => [
                    'back' => [
                        'type'       => 'Zend\Form\Element',
                        'attributes' => [
                            'name' => 'back',
                            'type' => 'hidden',
                        ],
                        'options'    => [],
                    ],
                ],
            ],
        ];

    public function getUtilityFieldsetsConfigs()
    {
        return $this->_utilityFieldsetsConfigs;
    }

    public function getFormConfig($cd)
    {
        $modelName     = $cd->model;
        $formConfig    = [
            'name'            => $modelName . 'Form',
            'group'           => 'form',
            'type'            => 'form',
            'options'         => [],
            'attributes'      => [
                'class'  => 'validate',
                'method' => 'post',
                'name'   => $modelName . 'form'
            ],
            // , 'action' => 'reg'
            'fieldsets'       => [],
            'elements'        => [],
            'filters'         => [],
            'validationGroup' => [],
        ];

        $_fsGroups     = [];
        $_filterGroups = [];
        foreach ($cd->fields as $field_name => $field_conf) {
            $_grp = $field_conf['group'];
            if ( !isset($_fsGroups[$_grp])) {
                $_fsGroups[$_grp]     = [];
                $_filterGroups[$_grp] = [];
            }

            $_field = $this->createFormElement($field_name, $field_conf);

            $_fsGroups[$_grp] += $_field['elements'];
            $_filterGroups[$_grp] += $_field['filters'];

            foreach (array_keys($_field['elements']) as $_k) {
                $formConfig['validationGroup'][$_grp][] = $_k;
            }
        }

        $fssConfigs = [];
        foreach ($cd->groups as $_grp => $_fls) {
            if (is_numeric($_grp)) {
                $_grp = $_fls;
                $_lbl = $cd->model . ' information';
                if ($_grp == 'fields') {
                    $_baseFieldSet = true;
                } else {
                    $_baseFieldSet = false;
                }
            } else {
                $_lbl          = $_fls['label'];
                $_baseFieldSet = isset($_fls['base']) && $_fls['base'] == true;
            }

            $fsConfig = [
                'name'            => $modelName . 'Fieldset',
                'group'           => $_grp,
                'type'            => 'fieldset',
                'options'         => ['label' => $_lbl],
                'attributes'      => ['name' => $_grp, 'class' => 'table'],
                'fieldsets'       => [],
                'elements'        => [],
                'validationGroup' => [],
            ];

            //Add When elements are created
            //
            if (isset($_fsGroups[$_grp])) {
                if ( !isset($formConfig['filters'][$_grp])) {
                    $formConfig['filters'][$_grp] = [];
                }
                $fsConfig['elements'] = $_fsGroups[$_grp];
                $formConfig['filters'][$_grp] += $_filterGroups[$_grp];
            }
            //elements created
            //

            $fssConfigs[$_grp]              = $fsConfig;
            $formConfig['fieldsets'][$_grp] = [
                'type' => $modelName . 'Fieldset'
            ];
            if ($_baseFieldSet == true) {
                $formConfig['fieldsets'][$_grp]
                    = ['options' => ['use_as_base_fieldset' => true]];
            }
        }
        $formConfig['fieldsets_configs'] = $fssConfigs;


        $ufs                             = $this->getUtilityFieldsetsConfigs();
        foreach ($ufs as $fieldSet) {
            $formConfig['fieldsets'][$fieldSet['name']]
                                                                = ['type' => $fieldSet['name']];
            $formConfig['fieldsets_configs'][$fieldSet['name']] = $fieldSet;
        }
        $cf = new ConfigForm();

        $cf->exchangeArray($formConfig);

        return $cf;
    }

    protected function createFormElement($name, $conf)
    {
        $type = $conf['type'];
//        $_elementConf                         = $this->_fieldtypes[ $type ][ 'formElement' ];
        $_elementConf                     = $this->getFieldTypesServiceVerify()
            ->getFormElement($type);
        $filter                           = $this->getFieldTypesServiceVerify()
            ->getInputFilter($type);
        $filter['name']                   = $name;
        $_elementConf['options']['label'] = isset($conf['label'])
            ? $conf['label'] : ucfirst($name);
        if ($type == 'lookup') {
            $name .= '_id';
            $filter['name'] = $name;
            $_where         = ['status_id' => [Status::NEW_, Status::NORMAL]];
            $_order         = $conf['fields'];
            $_fields        = array_keys($conf['fields']);
            $_mask          = null;
            if (isset($conf['query']) && strlen($conf['query'])) {
                $query   = $this->getQueryServiceVerify()->get($conf['query'])
                    ->process();
                $_where  = $query->getWhere();
                $_order  = $query->getOrder();
                $_fields = $query->getFields();

                $_mask = $query->getFormat('label');
            }

            $_lAll    = $this->getGatewayServiceVerify()->get($conf['model'])
                ->find($_where, $_order);
            $_options = [];
            foreach ($_lAll as $_lRow) {
                $_lLabel = '';
                $_lvalue = $_lRow->id();

                if ($_mask !== null && strlen($_mask)) {
                    $_vals = [];
                    foreach ($_fields as $field) {
                        $_vals[$field] = $_lRow->$field;
                    }
                    $_lLabel = vsprintf($_mask, $_vals);
                } else {
                    foreach ($_fields as $_k) {
                        if (strlen($_lLabel)) {
                            $_lLabel .= '  [ ';
                            $_lLabel .= $_lRow->$_k;
                            $_lLabel .= ' ] ';
                        } else {
                            $_lLabel .= $_lRow->$_k;
                        }
                    }
                }
                $_options[$_lvalue] = $_lLabel;
            }
            $_elementConf['options']['value_options'] += $_options;
        }

        if ($type == 'static_lookup') {
            $name .= '_id';
            $filter['name'] = $name;
            $_lAll          = $this->getConfigService()
                ->get('StaticDataSource', $conf['model'],
                    new StaticDataConfig());
            $_options       = [];
            foreach ($_lAll->options as $_key => $_lRow) {
                $_lLabel = $_lRow[$_lAll->attributes['select_field']];
                $_lvalue = $_key;

                $_options[$_lvalue] = $_lLabel;
            }
            if (isset($conf['default'])) {
                $_elementConf['options']['value_options'] = $_options;
//                $_elementConf[ 'attributes' ][ 'value' ]      = $conf[ 'default' ];
            } else {
                $_elementConf['options']['value_options'] += $_options;
            }
            $_elementConf['options']['label']
                = $conf['fields'][$_lAll->attributes['select_field']];
        }

        $_elementConf['attributes']['name'] = $name;
        if (isset($conf['required'])) {
            $_elementConf['attributes']['required'] = 'required';
            if (isset($_elementConf['options']['label_attributes']['class'])
                && strlen($_elementConf['options']['label_attributes']['class'])
            ) {
                $_elementConf['options']['label_attributes']['class'] .= ' required';
            } else {
                $_elementConf['options']['label_attributes']
                    = ['class' => 'required'];
            }
        }

        $result = [
            'filters'  => [$name => $filter],
            'elements' => [$name => $_elementConf]
        ];

//        $result = [ $name => $_elementConf ];

        return $result;
    }
}
