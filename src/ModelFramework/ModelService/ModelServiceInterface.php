<?php

namespace ModelFramework\ModelService;

use ModelFramework\DataModel\DataModel;
use ModelFramework\ModelService\ModelConfig\ModelConfig;
use ModelFramework\ModelService\ModelConfig\ParsedModelConfig;

interface ModelServiceInterface
{

    /**
     * @param string $modelName
     *
     * @return DataModel
     */
    public function get($modelName);

    /**
     * @param string $modelName
     *
     * @return DataModel
     */
    public function getModel($modelName);

    /**
     * @param string $model
     *
     * @return array
     */
    public function makeIndexes($model);

    /**
     * @param string $modelName
     *
     * @return ModelConfig
     * @throws \Exception
     */
    public function getModelConfig($modelName);

    /**
     * @param string $modelName
     *
     * @return ParsedModelConfig
     * @throws \Exception
     */
    public function getParsedModelConfig($modelName);

}
