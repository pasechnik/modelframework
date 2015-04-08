<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\ModelService\ModelConfigParser;

interface ModelConfigParserAwareInterface
{
    /**
     * @param ModelConfigParser $modelConfigParser
     *
     * @return $this
     */
    public function setModelConfigParser(ModelConfigParser $modelConfigParser);

    /**
     * @return ModelConfigParser
     */
    public function getModelConfigParser();

    /**
     * @return ModelConfigParser
     */
    public function getModelConfigParserVerify();
}
