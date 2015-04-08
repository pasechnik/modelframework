<?php

namespace ModelFramework\FilesystemService\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\AbstractAdapterFactory;
use ModelFramework\FilesystemService\Adapter\Wepo as Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WepoAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function doCreateService(ServiceLocatorInterface $serviceLocator)
    {

        $adapter = new Adapter(
            $serviceLocator->get('modelframeworkauthservice'),
            $this->options['key'],
            $this->options['api_url']
        );

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (!isset($this->options['key'])) {
            throw new \UnexpectedValueException("Missing 'key' as option");
        }
        if (!isset($this->options['api_url'])) {
            throw new \UnexpectedValueException("Missing 'api_url' as option");
        }
    }
}
