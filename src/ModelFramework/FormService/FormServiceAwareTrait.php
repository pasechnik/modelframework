<?php
/**
 * Class FormServiceAwareTrait
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

trait FormServiceAwareTrait
{
    /**
     * @var FormServiceInterface
     */
    private $_formService = null;

    /**
     * @param FormServiceInterface $formService
     *
     * @return $this
     */
    public function setFormService(FormServiceInterface $formService)
    {
        $this->_formService = $formService;

        return $this;
    }

    /**
     * @return FormServiceInterface
     */
    public function getFormService()
    {
        return $this->_formService;
    }

    /**
     * @return FormServiceInterface
     * @throws \Exception
     */
    public function getFormServiceVerify()
    {
        $_formService = $this->getFormService();
        if ($_formService == null || !$_formService instanceof FormServiceInterface) {
            throw new \Exception('FormService does not set in the FormServiceAware instance of '.
                                  get_class($this));
        }

        return $_formService;
    }
}
