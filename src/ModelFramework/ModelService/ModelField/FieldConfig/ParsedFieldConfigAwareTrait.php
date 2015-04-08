<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\ModelService\ModelField\FieldConfig;

use ModelFramework\Utility\Arr;

trait ParsedFieldConfigAwareTrait
{

    /**
     * @var array
     */
    private $_parsedFieldConfig = [];

    /**
     * @param array $parsedFieldConfig
     *
     * @return $this
     */
    public function setParsedFieldConfig(array $parsedFieldConfig)
    {
        $this->_parsedFieldConfig = $parsedFieldConfig;

        return $this;
    }

    /**
     * @return array
     */
    public function getParsedFieldConfig()
    {
        return $this->_parsedFieldConfig;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getParsedFieldConfigVerify()
    {
        $parsedFieldConfig = $this->getParsedFieldConfig();
        if ($parsedFieldConfig == null || !is_array($parsedFieldConfig)) {
            throw new \Exception('ParsedFieldConfig is not set in '
                . get_class($this));
        }

        return $this->getParsedFieldConfig();
    }

    public function addParsedConfig(array $a)
    {
        return $this->setParsedFieldConfig(
            Arr::merge($this->getParsedFieldConfig(), $a)
        );
    }
}
