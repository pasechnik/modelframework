<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\ModelService\ModelField\FieldConfig;

trait FieldConfigAwareTrait
{

    /**
     * @var FieldConfigInterface
     */
    private $_fieldConfig = null;

    /**
     * @param array|FieldConfigInterface $aConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function setFieldConfig($aConfig)
    {
        if ($aConfig instanceof FieldConfigInterface) {
            $this->_fieldConfig = $aConfig;
        } elseif (is_array($aConfig)) {
            $this->_fieldConfig = $this->parseFieldConfigArray($aConfig);
        } else {
            throw new \Exception('Wrong type of config ');
        }
        return $this;
    }

    /**
     * @return FieldConfigInterface
     */
    public function getFieldConfig()
    {
        return $this->_fieldConfig;
    }

    /**
     * @return FieldConfigInterface
     * @throws \Exception
     */
    public function getFieldConfigVerify()
    {
        $fieldConfig = $this->getFieldConfig();
        if ($fieldConfig == null
            || !$fieldConfig instanceof FieldConfigInterface
        ) {
            throw new \Exception('FieldConfig is not set in '
                . get_class($this));
        }

        return $fieldConfig;

    }

}
