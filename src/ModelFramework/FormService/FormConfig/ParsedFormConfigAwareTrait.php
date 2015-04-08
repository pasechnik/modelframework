<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\FormService\FormConfig;

use ModelFramework\Utility\Arr;

trait ParsedFormConfigAwareTrait
{

    private $_parsedFormConfig = null;

    /**
     * @param ParsedFormConfig $parsedFormConfig
     *
     * @return $this
     */
    public function setParsedFormConfig(
        ParsedFormConfig $parsedFormConfig = null
    ) {
        if ($parsedFormConfig === null) {
            $parsedFormConfig = new ParsedFormConfig();
        }
        $this->_parsedFormConfig = $parsedFormConfig;

        return $this;
    }

    /**
     * @return ParsedFormConfig
     */
    public function getParsedFormConfig()
    {
        return $this->_parsedFormConfig;
    }

    /**
     * @return ParsedFormConfig
     * @throws \Exception
     */
    public function getParsedFormConfigVerify()
    {
        $parsedFormConfig = $this->getParsedFormConfig();
        if ($parsedFormConfig == null
            || !$parsedFormConfig instanceof ParsedFormConfig
        ) {
            throw new \Exception('ParsedFormConfig is not set in '
                . get_class($this));
        }

        return $this->getParsedFormConfig();
    }

    public function addParsedConfig(array $a)
    {
        $parsedFormConfig = $this->getParsedFormConfig();
        if ($parsedFormConfig === null) {
            $parsedFormConfig = new ParsedFormConfig();
        }
        $conf = $parsedFormConfig->toArray();
        $newConf = Arr::merge($conf, $a);
        $parsedFormConfig->exchangeArray($newConf);
        return $this->setParsedFormConfig($parsedFormConfig);
    }
}
