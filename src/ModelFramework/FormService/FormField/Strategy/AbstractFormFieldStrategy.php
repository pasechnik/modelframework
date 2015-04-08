<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:09
 */

namespace ModelFramework\FormService\FormField\Strategy;

use ModelFramework\AclService\AclConfig\AclConfigAwareInterface;
use ModelFramework\AclService\AclConfig\AclConfigAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelAwareInterface;
use ModelFramework\DataModel\DataModelAwareTrait;
use ModelFramework\FieldTypesService\FormElementConfig\FormElementConfigAwareTrait;
use ModelFramework\FieldTypesService\FormElementConfig\FormElementConfigInterface;
use ModelFramework\FieldTypesService\InputFilterConfig\InputFilterConfigAwareTrait;
use ModelFramework\FieldTypesService\InputFilterConfig\InputFilterConfigInterface;
use ModelFramework\FormService\LimitFieldsAwareInterface;
use ModelFramework\FormService\LimitFieldsAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfig;
use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfigInterface;
use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfigAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;

abstract class AbstractFormFieldStrategy
    implements FormFieldStrategyInterface, AclConfigAwareInterface,
               LimitFieldsAwareInterface, QueryServiceAwareInterface,
               GatewayServiceAwareInterface, ConfigServiceAwareInterface,
               DataModelAwareInterface
{

    use FieldConfigAwareTrait, InputFilterConfigAwareTrait,
        FormElementConfigAwareTrait, AclConfigAwareTrait, LimitFieldsAwareTrait,
        QueryServiceAwareTrait, GatewayServiceAwareTrait,
        ConfigServiceAwareTrait, DataModelAwareTrait;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName( $name )
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
    public function parseFieldConfigArray( array $aConfig )
    {
        $fieldConfig = new FieldConfig();
        $fieldConfig->exchangeArray( $aConfig );
        return $fieldConfig;
    }

    /**
     * @return $this
     */
    public function parse()
    {
        return $this->s( $this->getFieldConfigVerify(),
            $this->getFormElementConfigVerify(),
            $this->getInputFilterConfigVerify() );
    }

    /**
     * @return $this
     */
    public function init()
    {

    }

    protected function isAllowed( $name )
    {
        $aclConfig = $this->getAclConfig();
        if ($aclConfig == null) {
            return true;
        }
        if (isset( $aclConfig->fields[ $name ] ) &&
            $aclConfig->fields[ $name ] == 'write'
        ) {
            return true;
        }

        return false;
    }

    protected function isNotLimited( $name )
    {
        $fields = $this->getLimitFields();
        if (empty( $fields )) {
            return true;
        }
        if (in_array( $name, $fields )) {
            return true;
        }

        return false;
    }

    abstract public function s(
        FieldConfigInterface $conf,
        FormElementConfigInterface $_formElement,
        InputFilterConfigInterface $_inputFilter
    );
}
