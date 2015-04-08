<?php
/**
 * Class ViewService
 * @package ModelFramework\ViewService
 */

namespace ModelFramework\QueryService;

use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\QueryService\QueryConfig\QueryConfig;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;

class QueryService
    implements QueryServiceInterface, ConfigServiceAwareInterface, AuthServiceAwareInterface, ParamsAwareInterface
{
    use ConfigServiceAwareTrait, AuthServiceAwareTrait, ParamsAwareTrait;

    /**
     * @param string $queryName
     *
     * @return Query|QueryInterface
     * @throws \Exception
     */
    public function get($queryName)
    {
        return $this->getQuery($queryName);
    }

    /**
     * @param string $queryName
     *
     * @return Query|QueryInterface
     * @throws \Exception
     */
    public function getQuery($queryName)
    {
        if (is_array($queryName)) {
            throw new \Exception('Change QueryConfig to the valid QueryName. I have got an array ');
        }

        // this object will deal with all view of model stuff
        $query = new Query();

        // we want modelView get to know what to show and how
        $queryConfig = $this->getConfigServiceVerify()->getByObject($queryName,  new QueryConfig());

        if ($queryConfig == null) {
            throw new \Exception('Please fill QueryConfig for the '.$queryName.'. I can\'t get it out');
        }

        if ($this->getParams() !== null) {
            $query->setParams($this->getParams());
        }
        $query->setQueryConfig($queryConfig);

        $query->setAuthService($this->getAuthServiceVerify());

        $query->init();

        return $query;
    }
}
