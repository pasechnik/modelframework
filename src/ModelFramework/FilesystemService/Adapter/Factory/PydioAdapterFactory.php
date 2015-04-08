<?php

namespace ModelFramework\FilesystemService\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\AbstractAdapterFactory;
use ModelFramework\FilesystemService\Adapter\Pydio as Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PydioAdapterFactory extends AbstractAdapterFactory implements FactoryInterface
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
            $this->options['login'],
            $this->options['pass'],
            $this->options['api_url'],
            $this->options['workspace']
        );

        return $adapter;
    }

    /**
     * @inheritdoc
     */
    protected function validateConfig()
    {
        if (!isset($this->options['login'])) {
            throw new \UnexpectedValueException("Missing 'login' as option");
        }
        if (!isset($this->options['pass'])) {
            throw new \UnexpectedValueException("Missing 'pass' as option");
        }
        if (!isset($this->options['api_url'])) {
            throw new \UnexpectedValueException("Missing 'api_url' as option");
        }
        if (!isset($this->options['workspace'])) {
            throw new \UnexpectedValueException("Missing 'workspace' as option");
        }
    }
}
