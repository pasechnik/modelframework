<?php

namespace ModelFramework\ModelService;

interface ModelServiceAwareInterface
{
    /**
     * @param ModelServiceInterface $modelService
     *
     * @return $this
     */
    public function setModelService(ModelServiceInterface $modelService);

    /**
     * @return ModelServiceInterface
     */
    public function getModelService();

    /**
     * @return ModelServiceInterface
     * @throws \Exception
     */
    public function getModelServiceVerify();
}
