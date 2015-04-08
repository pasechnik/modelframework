<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:10
 */

namespace ModelFramework\FormService\FormField\Strategy;


use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfigAwareInterface;

interface FormFieldStrategyInterface
    extends FieldConfigAwareInterface
{

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName( $name );

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
