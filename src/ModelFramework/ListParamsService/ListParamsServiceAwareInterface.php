<?php
/**
 * Class ListParamsServiceAwareInterface
 * @package ModelFramework\ListParamsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ListParamsService;

interface ListParamsServiceAwareInterface
{
    /**
     * @param ListParamsServiceInterface $listparamsservice
     *
     * @return $this
     */
    public function setListParamsService(ListParamsServiceInterface $listparamsservice);

    /**
     * @return ListParamsServiceInterface
     */
    public function getListParamsService();

    /**
     * @return ListParamsServiceInterface
     * @throws \Exception
     */
    public function getListParamsServiceVerify();
}
