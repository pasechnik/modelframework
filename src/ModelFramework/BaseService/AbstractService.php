<?php

namespace ModelFramework\BaseService;

use Zend\ServiceManager\ServiceLocatorAwareInterface;

abstract class AbstractService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
}
