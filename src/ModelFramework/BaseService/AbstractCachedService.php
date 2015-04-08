<?php

namespace ModelFramework\BaseService;

use ModelFramework\CacheService\CacheTrait;

abstract class AbstractCachedService extends AbstractService
{
    use CacheTrait;
}
