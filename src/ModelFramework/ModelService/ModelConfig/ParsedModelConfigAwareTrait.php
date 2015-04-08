<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\ModelService\ModelConfig;

use ModelFramework\Utility\Arr;

trait ParsedModelConfigAwareTrait
{

    /**
     * @var ParsedModelConfig
     */
    private $_parsedModelConfig = null;

    /**
     * @param ParsedModelConfig $parsedModelConfig
     *
     * @return $this
     */
    public function setParsedModelConfig(ParsedModelConfig $parsedModelConfig = null )
    {
        $this->_parsedModelConfig = $parsedModelConfig;

        return $this;
    }

    /**
     * @return ParsedModelConfig
     */
    public function getParsedModelConfig()
    {
        return $this->_parsedModelConfig;
    }

    /**
     * @return ParsedModelConfig
     * @throws \Exception
     */
    public function getParsedModelConfigVerify()
    {
        $parsedModelConfig = $this->getParsedModelConfig();
        if ($parsedModelConfig == null
            || !$parsedModelConfig instanceof ParsedModelConfig
        ) {
            throw new \Exception('ParsedModelConfig is not set in '
                . get_class($this));
        }

        return $this->getParsedModelConfig();
    }

    public function addParsedConfig(array $a)
    {
        $parsedModelConfig = $this->getParsedModelConfig();
        if ($parsedModelConfig === null) {
            $parsedModelConfig = new ParsedModelConfig();
        }
        $conf = $parsedModelConfig->toArray();
        $newConf = Arr::merge($conf, $a);
        $parsedModelConfig->exchangeArray($newConf);
        return $this->setParsedModelConfig($parsedModelConfig);
    }
}
