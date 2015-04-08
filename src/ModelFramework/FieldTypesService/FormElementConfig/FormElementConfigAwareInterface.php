<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\FieldTypesService\FormElementConfig;

interface FormElementConfigAwareInterface
{

    /**
     * @param array|FormElementConfigInterface $formElement
     *
     * @return $this
     * @throws \Exception
     */
    public function setFormElementConfig( $formElement );

    /**
     * @param array $config
     *
     * @return $this
     * @throws \Exception
     */
    public function parseFormElementArray( array $config );

    /**
     * @return FormElementConfigInterface
     */
    public function getFormElementConfig();

    /**
     * @return FormElementConfigInterface
     * @throws \Exception
     */
    public function getFormElementConfigVerify();
}
