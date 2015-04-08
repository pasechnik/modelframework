<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\ModelService\ModelConfig;

interface ParsedModelConfigAwareInterface
{
    /**
     * @param ParsedModelConfig $parsedModelConfig
     *
     * @return $this
     */
    public function setParsedModelConfig(ParsedModelConfig $parsedModelConfig = null);

    /**
     * @return ParsedModelConfig
     */
    public function getParsedModelConfig();

    /**
     * @return ParsedModelConfig
     * @throws \Exception
     */
    public function getParsedModelConfigVerify();
}
