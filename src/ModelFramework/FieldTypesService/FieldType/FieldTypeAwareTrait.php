<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\FieldTypesService\FieldType;

trait FieldTypeAwareTrait
{

    /**
     * @var FieldTypeInterface
     */
    private $_fieldType = null;

    /**
     * @param array $aConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function parseFieldTypeArray(array $aConfig)
    {
        $fieldType = new FieldType();
        $fieldType->exchangeArray($aConfig);
        return $fieldType;
    }

    /**
     * @param array|FieldTypeInterface $aConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function setFieldType($aConfig)
    {
        if ($aConfig instanceof FieldTypeInterface) {
            $this->_fieldType = $aConfig;
        } elseif (is_array($aConfig)) {
            $this->_fieldType = $this->parseFieldTypeArray($aConfig);
        } else {
            throw new \Exception('Wrong type of config ');
        }
        return $this;
    }

    /**
     * @return FieldTypeInterface
     */
    public function getFieldType()
    {
        return $this->_fieldType;
    }

    /**
     * @return FieldTypeInterface
     * @throws \Exception
     */
    public function getFieldTypeVerify()
    {
        $fieldType = $this->getFieldType();
        if ($fieldType == null
            || !$fieldType instanceof FieldTypeInterface
        ) {
            throw new \Exception('FieldType is not set in '
                . get_class($this));
        }

        return $fieldType;

    }

}
