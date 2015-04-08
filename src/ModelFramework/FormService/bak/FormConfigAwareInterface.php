<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\FormService\FormConfig;

interface FormConfigAwareInterface
{
    /**
     * @param FormConfig $formConfig
     *
     * @return $this
     */
    public function setFormConfig(FormConfig $formConfig);

    /**
     * @return FormConfig
     */
    public function getFormConfig();

    /**
     * @return FormConfig
     * @throws \Exception
     */
    public function getFormConfigVerify();
}
