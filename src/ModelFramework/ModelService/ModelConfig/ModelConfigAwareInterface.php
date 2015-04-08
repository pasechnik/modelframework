<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\ModelService\ModelConfig;

interface ModelConfigAwareInterface
{
    /**
     * @param ModelConfig $modelConfig
     *
     * @return $this
     */
    public function setModelConfig(ModelConfig $modelConfig);

    /**
     * @return ModelConfig
     */
    public function getModelConfig();

    /**
     * @return ModelConfig
     * @throws \Exception
     */
    public function getModelConfigVerify();
}
