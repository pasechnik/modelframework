<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\ModelService\ModelField\FieldConfig;

interface FieldConfigAwareInterface
{
    /**
     * @param array|FieldConfigInterface $aConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function setFieldConfig($aConfig);

    /**
     * @param array $aConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function parseFieldConfigArray( array $aConfig);

    /**
     * @return FieldConfigInterface
     */
    public function getFieldConfig();

    /**
     * @return FieldConfigInterface
     * @throws \Exception
     */
    public function getFieldConfigVerify();
}
