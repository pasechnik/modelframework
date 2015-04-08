<?php

namespace ModelFramework\ModelService;

trait ModelServiceAwareTrait
{
    /**
     * @var ModelServiceInterface
     */
    private $_modelService = null;

    /**
     * @param ModelServiceInterface $modelService
     *
     * @return $this
     */
    public function setModelService(ModelServiceInterface $modelService)
    {
        $this->_modelService = $modelService;

        return $this;
    }

    /**
     * @return ModelServiceInterface
     */
    public function getModelService()
    {
        return $this->_modelService;
    }

    /**
     * @return ModelServiceInterface
     * @throws \Exception
     */
    public function getModelServiceVerify()
    {
        $_modelService =  $this->getModelService();
        if ($_modelService == null || ! $_modelService instanceof ModelServiceInterface) {
            throw new \Exception('ModelService does not set in the ModelServiceAware instance of '.
                                  get_class($this));
        }

        return $_modelService;
    }
}
