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
use ModelFramework\Utility\Arr;

class EmailStrategy
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

        $_fields = Arr::merge($_fields, [
            $this->getName() . '_id' => [
                'type'      => 'field',
                'fieldtype' => 'source',
                'datatype'  => 'string',
                'default'   => 0,
                'label'     => '',
//                'source'    => $this->getName(),
            ]
        ]);

        $result = [
            'labels'    => $_labels,
            'fields'    => $_fields,
            'joins'     => $_joins,
            'fieldsets' => $_fieldSets,
        ];

        return $result;
    }

}
