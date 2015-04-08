<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:09
 */

namespace ModelFramework\FormService\FormField\Strategy;

use ModelFramework\FieldTypesService\FieldType\FieldTypeAwareTrait;
use ModelFramework\FieldTypesService\FieldType\FieldTypeInterface;
use ModelFramework\FormService\FormField\FieldConfig\FieldConfig;
use ModelFramework\FormService\FormField\FieldConfig\FieldConfigInterface;
use ModelFramework\FormService\FormField\FieldConfig\FieldConfigAwareTrait;

class FieldStrategy
    implements FormFieldStrategyInterface
{

    use FieldConfigAwareTrait, FieldTypeAwareTrait;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getFieldConfigVerify()->type;
    }

    /**
     * @param array $aConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function parseFieldConfigArray(array $aConfig)
    {
        $fieldConfig = new FieldConfig();
        $fieldConfig->exchangeArray($aConfig);
        return $fieldConfig;
    }

    /**
     * @return $this
     */
    public function parse()
    {
        return $this->s($this->getFieldConfigVerify(),
            $this->getFieldTypeVerify());
    }

    /**
     * @return $this
     */
    public function init()
    {

    }

    public function s(
        FieldConfigInterface $conf,
        FieldTypeInterface $_fieldType
    ) {
        $_fieldSets        = [];
        $_joins            = [];
        $_fieldType->label = isset($conf->label) ? $conf->label
            : ucfirst($this->getName());

        if (isset($conf->group)) {
            $_fieldSets[$conf->group]['elements'][$this->getName()]
                               = $_fieldType->label;
            $_fieldType->group = $conf->group;
        }
        //FIXME this does not work for lookup fields, only for source fields. Need update.
        $_fieldType->default = isset($conf->default) ? $conf->default : '';
        $_fieldType->source  = $this->getName();
        $_fields             = [$this->getName() => $_fieldType->toArray()];
        $_labels             = [$this->getName() => $_fieldType->label];

        /* :FIXME: */
//        $_utility = $this->getFieldPart($conf->type, 'utility');
//
//        if (count($_utility)) {
//            $_fields = array_merge($_fields, $_utility);
//        }
        /**/

        $result = [
            'labels'    => $_labels,
            'fields'    => $_fields,
            'joins'     => $_joins,
            'fieldsets' => $_fieldSets,
        ];

        return $result;
    }

    protected function f($name, $_elementConf)
    {
        $type = $this->getType();
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
