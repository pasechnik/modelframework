<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\ModelService\ModelConfig;

trait ModelConfigAwareTrait
{
    private $_modelConfig = null;

    /**
     * @param ModelConfig $modelConfig
     *
     * @return $this
     */
    public function setModelConfig(ModelConfig $modelConfig)
    {
        $this->_modelConfig = $modelConfig;

        return $this;
    }

    /**
     * @return ModelConfig
     */
    public function getModelConfig()
    {
        return $this->_modelConfig;
    }

    /**
     * @return ModelConfig
     * @throws \Exception
     */
    public function getModelConfigVerify()
    {
        $modelConfig = $this->getModelConfig();
        if ($modelConfig == null || !$modelConfig instanceof ModelConfig ) {
            throw new \Exception('ModelConfig is not set in ' . get_class($this) );
        }

        return $modelConfig;
    }
}
