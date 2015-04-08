<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:10
 */

namespace ModelFramework\ModelService\ModelField\Strategy;

use ModelFramework\FieldTypesService\FieldType\FieldTypeAwareInterface;
use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfigAwareInterface;

interface ModelFieldStrategyInterface extends FieldConfigAwareInterface, FieldTypeAwareInterface
{

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return $this
     */
    public function parse();

    /**
     * @return $this
     */
    public function init();

}
