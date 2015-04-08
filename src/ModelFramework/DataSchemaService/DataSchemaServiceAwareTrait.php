<?php
/**
 * Trait DataSchemaServiceAwareTrait
 * @package ModelFramework\DataSchemaService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataSchemaService;

trait DataSchemaServiceAwareTrait
{
    private $_dataSchemaService = null;

    /**
     * @param DataSchemaServiceInterface $dataSchemaService
     *
     * @return $this
     */
    public function setDataSchemaService(DataSchemaServiceInterface $dataSchemaService)
    {
        $this->_dataSchemaService = $dataSchemaService;

        return $this;
    }

    /**
     * @return DataSchemaServiceInterface
     */
    public function getDataSchemaService()
    {
        return $this->_dataSchemaService;
    }

    /**
     * @return DataSchemaServiceInterface
     * @throws \Exception
     */
    public function getDataSchemaServiceVerify()
    {
        $_dataSchemaService = $this->getDataSchemaService();
        if ($_dataSchemaService == null || !$_dataSchemaService instanceof DataSchemaServiceInterface) {
            throw new \Exception('DataSchemaService does not set in the DataSchemaServiceAware instance of '.
                                  get_class($this));
        }

        return $_dataSchemaService;
    }
}
