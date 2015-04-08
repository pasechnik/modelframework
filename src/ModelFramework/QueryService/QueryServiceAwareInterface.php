<?php
/**
 * Class QueryServiceAwareInterface
 * @package ModelFramework\QueryService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService;

interface QueryServiceAwareInterface
{
    /**
     * @param QueryServiceInterface $queryService
     *
     * @return $this
     */
    public function setQueryService(QueryServiceInterface $queryService);

    /**
     * @return QueryServiceInterface
     */
    public function getQueryService();

    /**
     * @return QueryServiceInterface
     * @throws \Exception
     */
    public function getQueryServiceVerify();
}
