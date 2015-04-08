<?php

namespace ModelFramework\BaseService;

use Zend\ServiceManager\ServiceLocatorInterface;

interface ServiceLocatorAwareInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return $this
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator);

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator();

    /**
     * @return ServiceLocatorInterface
     *
     * @throws \Exception
     */
    public function getServiceLocatorVerify();
}
