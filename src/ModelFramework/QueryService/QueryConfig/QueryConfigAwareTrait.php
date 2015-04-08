<?php
/**
 * Class QueryConfigAwareTrait
 * @package ModelFramework\DataModel\Custom
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\QueryConfig;

use ModelFramework\DataModel\DataModelInterface;

trait QueryConfigAwareTrait
{
    /**
     * @var QueryConfig
     */
    private $_queryConfig = null;

    /**
     * @param QueryConfig|DataModelInterface $queryConfig
     *
     * @return $this
     */
    public function setQueryConfig(QueryConfig $queryConfig)
    {
        $this->_queryConfig = $queryConfig;
    }

    /**
     * @return QueryConfig
     *
     */
    public function getQueryConfig()
    {
        return $this->_queryConfig;
    }

    /**
     * @return QueryConfig
     * @throws \Exception
     */
    public function getQueryConfigVerify()
    {
        $queryConfig = $this->getQueryConfig();
        if ($queryConfig == null || !$queryConfig instanceof QueryConfig) {
            throw new \Exception('Query Config does not set set in QueryConfigAware instance of '.
                                  get_class($this));
        }

        return $queryConfig;
    }
}
