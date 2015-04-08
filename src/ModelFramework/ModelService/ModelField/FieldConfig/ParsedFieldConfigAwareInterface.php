<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\ModelService\ModelField\FieldConfig;

interface ParsedFieldConfigAwareInterface
{
    /**
     * @param array $parsedFieldConfig
     *
     * @return $this
     */
    public function setParsedFieldConfig(array $parsedFieldConfig);

    /**
     * @return array
     */
    public function getParsedFieldConfig();

    /**
     * @return array
     * @throws \Exception
     */
    public function getParsedFieldConfigVerify();
}
