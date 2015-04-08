<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 11:18 AM
 */

namespace ModelFramework\ModelService\ModelConfigParserService;

interface ModelConfigParserServiceInterface
{

    /**
     * @param string $modelName
     *
     * @return array
     */
    public function getModelConfig($modelName);

    /**
     * Returns array with all registered models names
     *
     * @return array
     */
    public function getAllModelNames();

    /**
     * Calculates all available indexes for the current config
     *
     * @param $model
     *
     * @return array
     * @throws \Exception
     */
    public function getAvailableIndexes($model);
}
