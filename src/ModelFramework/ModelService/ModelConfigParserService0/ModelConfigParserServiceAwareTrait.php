<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 11:19 AM
 */

namespace ModelFramework\ModelService\ModelConfigParserService;

trait ModelConfigParserServiceAwareTrait
{
    /**
     * @var ModelConfigParserServiceInterface
     */
    private $_modelConfigParserService = null;

    /**
     * @param ModelConfigParserServiceInterface $modelConfigParserService
     *
     * @return $this
     */
    public function setModelConfigParserService(ModelConfigParserServiceInterface $modelConfigParserService)
    {
        $this->_modelConfigParserService = $modelConfigParserService;
    }

    /**
     * @return ModelConfigParserServiceInterface
     */
    public function getModelConfigParserService()
    {
        return $this->_modelConfigParserService;
    }

    /**
     * @return ModelConfigParserServiceInterface
     * @throws \Exception
     */
    public function getModelConfigParserServiceVerify()
    {
        $modelConfigParserService = $this->getModelConfigParserService();
        if ($modelConfigParserService == null ||
             !$modelConfigParserService instanceof ModelConfigParserServiceInterface
        ) {
            throw new \Exception('ModelConfigParserService does not set in the ModelConfigParserServiceAware instance of '.
                                  get_class($this));
        }

        return $modelConfigParserService;
    }
}
