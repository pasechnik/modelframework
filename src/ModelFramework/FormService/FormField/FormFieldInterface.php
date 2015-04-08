<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:31
 */

namespace ModelFramework\FormService\FormField;

use ModelFramework\AclService\AclConfig\AclConfigAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FormService\LimitFieldsAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\ModelService\ModelField\FieldConfig\ParsedFieldConfigAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareInterface;

interface FormFieldInterface extends ParsedFieldConfigAwareInterface,
                                     FieldTypesServiceAwareInterface,
                                     AclConfigAwareInterface,
                                     LimitFieldsAwareInterface,
                                     QueryServiceAwareInterface,
                                     GatewayServiceAwareInterface,
                                     ConfigServiceAwareInterface
{

    /**
     * @param string $type
     *
     * @return $this
     */
    public function chooseStrategy($type);

    /**
     * @return $this
     */
    public function init();

    /**
     * @return $this
     */
    public function parse();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setFieldConfig($config);


    /**
     * @return string
     */
    public function getType();

}
