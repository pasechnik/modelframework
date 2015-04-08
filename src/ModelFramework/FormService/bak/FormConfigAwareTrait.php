<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\FormService\FormConfig;

trait FormConfigAwareTrait
{
    private $_formConfig = null;

    /**
     * @param FormConfig $formConfig
     *
     * @return $this
     */
    public function setFormConfig(FormConfig $formConfig)
    {
        $this->_formConfig = $formConfig;

        return $this;
    }

    /**
     * @return FormConfig
     */
    public function getFormConfig()
    {
        return $this->_formConfig;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getFormConfigVerify()
    {
        $formConfig = $this->getFormConfig();
        if ($formConfig == null || !$formConfig instanceof FormConfig ) {
            throw new \Exception('FormConfig is not set in ' . get_class($this) );
        }

        return $formConfig;
    }
}
