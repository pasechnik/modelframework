<?php
/**
 * Class QueryServiceInterface
 * @package ModelFramework\QueryService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService;

interface QueryServiceInterface
{
    /**
     * @param string $key
     *
     * @return Query|QueryInterface
     * @throws \Exception
     */
    public function getQuery($key);

    /**
     * @param string $key
     *
     * @return Query|QueryInterface
     * @throws \Exception
     */
    public function get($key);
}
