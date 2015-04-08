<?php
/**
 * Class ListParamsServiceAwareTrait
 * @package ModelFramework\ListParamsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ListParamsService;

trait ListParamsServiceAwareTrait
{
    /**
     * @var ListParamsServiceInterface
     */
    private $_listParamsService = null;

    /**
     * @param ListParamsServiceInterface $listParamsService
     *
     * @return $this
     */
    public function setListParamsService(ListParamsServiceInterface $listParamsService)
    {
        $this->_listParamsService = $listParamsService;
    }

    /**
     * @return ListParamsServiceInterface
     */
    public function getListParamsService()
    {
        return $this->_listParamsService;
    }

    /**
     * @return ListParamsServiceInterface
     * @throws \Exception
     */
    public function getListParamsServiceVerify()
    {
        $_listParamsService = $this->getListParamsService();
        if ($_listParamsService == null || !$_listParamsService instanceof ListParamsServiceInterface) {
            throw new \Exception('ListParamsService does not set in the ListParamsServiceAware instance of '.
                                  get_class($this));
        }

        return $_listParamsService;
    }
}
