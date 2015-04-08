<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:09
 */

namespace ModelFramework\ModelService\ModelField\Strategy;

use ModelFramework\FieldTypesService\FieldType\FieldTypeAwareTrait;
use ModelFramework\FieldTypesService\FieldType\FieldTypeInterface;
use ModelFramework\ModelService\ModelField\FieldConfig\LookupConfig;
use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfigInterface;
use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfigAwareTrait;

class StaticLookupStrategy
    implements ModelFieldStrategyInterface
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
        $lookupConfig = new LookupConfig();
        $lookupConfig->exchangeArray($aConfig);
        return $lookupConfig;
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
        $_labels           = [];

        $_sign       = '_';
        $_joinfields = [];
        $_i          = 0;
        $_fields     = [];
        foreach ($conf->fields as $_jfield => $_jlabel) {
            if ( !$_i++) {
                $_fieldType->alias = $this->getName() . $_sign . $_jfield;
            }
            $_fields[$this->getName() . $_sign . $_jfield]     = [
                'type'      => 'alias',
                'fieldtype' => 'alias',
                'datatype'  => 'string',
                'default'   => '',
                //              'source'    => $this->getName() . $_sign . 'id',
                'label'     => $_jlabel,
                'group'     => isset($conf->group) ? $conf->group
                    : 'fields',
            ];
            $_labels[$this->getName() . $_sign . $_jfield]     = $_jlabel;
            $_joinfields[$this->getName() . $_sign . $_jfield] = $_jfield;
            if (isset($conf->group)) {
                $_fieldSets[$conf->group]['elements'][$this->getName() . $_sign
                . $_jfield]
                                   = $_jlabel;
                $_fieldType->group = $conf->group;
            }
        }
        $_joins[]                                  = [
            'model'  => $conf->model,
            'on'     => [$this->getName() . $_sign . 'id' => '_id'],
            'fields' => $_joinfields,
            'type'   => $conf->type,
        ];
        $_fieldType->source                        = $this->getName();
        $_fieldType->default                       = isset($conf->default)
            ? $conf->default
            : '';
        $_fields[$this->getName() . $_sign . 'id'] = $_fieldType->toArray();
        $_labels[$this->getName() . $_sign . 'id'] = $_jlabel;
//        $this->getName() .= '_id';
        $_fieldSets[$conf->group]['elements'][$this->getName() . $_sign . 'id']
            = $_jlabel;

        $result = [
            'labels'    => $_labels,
            'fields'    => $_fields,
            'joins'     => $_joins,
            'fieldsets' => $_fieldSets,
        ];

        return $result;
    }

}
