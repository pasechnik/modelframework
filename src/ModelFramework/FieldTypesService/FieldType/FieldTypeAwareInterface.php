<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\FieldTypesService\FieldType;

interface FieldTypeAwareInterface
{

    /**
     * @param array|FieldTypeInterface $aConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function setFieldType($aConfig);

    /**
     * @param array $aConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function parseFieldTypeArray(array $aConfig);

    /**
     * @return FieldTypeInterface
     */
    public function getFieldType();

    /**
     * @return FieldTypeInterface
     * @throws \Exception
     */
    public function getFieldTypeVerify();
}
