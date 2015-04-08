<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\ModelService\ModelConfigParser;

trait ModelConfigParserAwareTrait
{
    private $_modelConfigParser = null;

    /**
     * @param ModelConfigParser $modelConfigParser
     *
     * @return $this
     */
    public function setModelConfigParser(ModelConfigParser $modelConfigParser)
    {
        $this->_modelConfigParser = $modelConfigParser;

        return $this;
    }

    /**
     * @return ModelConfigParser
     */
    public function getModelConfigParser()
    {
        return $this->_modelConfigParser;
    }

    /**
     * @return ModelConfigParser
     * @throws \Exception
     */
    public function getModelConfigParserVerify()
    {
        $modelConfigParser = $this->getModelConfigParser();
        if ($modelConfigParser == null || !$modelConfigParser instanceof ModelConfigParser) {
            throw new \Exception('ModelConfigParser is not set in ' . get_class($this) );
        }

        return $this->getModelConfigParser();
    }
}
