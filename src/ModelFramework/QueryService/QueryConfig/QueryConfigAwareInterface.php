<?php
/**
 * Class QueryConfigAwareInterface
 * @package ModelFramework\QueryService\QueryConfig
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\QueryConfig;

interface QueryConfigAwareInterface
{
    /**
     * @param QueryConfig $queryConfig
     *
     * @return $this
     */
    public function setQueryConfig(QueryConfig $queryConfig);

    /**
     * @return QueryConfig
     */
    public function getQueryConfig();

    /**
     * @return QueryConfig
     * @throws \Exception
     */
    public function getQueryConfigVerify();
}
