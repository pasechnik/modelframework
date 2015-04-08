<?php
/**
 * Class FormServiceAwareInterface
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

interface FormServiceAwareInterface
{
    /**
     * @param FormServiceInterface $formService
     *
     * @return $this
     */
    public function setFormService(FormServiceInterface $formService);

    /**
     * @return FormServiceInterface
     */
    public function getFormService();

    /**
     * @return FormServiceInterface
     * @throws \Exception
     */
    public function getFormServiceVerify();
}
