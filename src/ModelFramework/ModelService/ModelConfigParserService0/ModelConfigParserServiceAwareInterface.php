<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 11:18 AM
 */

namespace ModelFramework\ModelService\ModelConfigParserService;

interface ModelConfigParserServiceAwareInterface
{
    /**
     * @param ModelConfigParserServiceInterface $modelConfigParserService
     *
     * @return $this
     */
    public function setModelConfigParserService(ModelConfigParserServiceInterface $modelConfigParserService);

    /**
     * @return ModelConfigParserServiceInterface
     */
    public function getModelConfigParserService();

    /**
     * @return ModelConfigParserServiceInterface
     * @throws \Exception
     */
    public function getModelConfigParserServiceVerify();
}
