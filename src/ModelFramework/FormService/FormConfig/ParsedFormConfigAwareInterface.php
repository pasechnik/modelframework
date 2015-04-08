<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\FormService\FormConfig;

interface ParsedFormConfigAwareInterface
{

    /**
     * @param ParsedFormConfig $parsedFormConfig
     *
     * @return $this
     */
    public function setParsedFormConfig(
        ParsedFormConfig $parsedFormConfig = null
    );

    /**
     * @return ParsedFormConfig
     */
    public function getParsedFormConfig();

    /**
     * @return ParsedFormConfig
     * @throws \Exception
     */
    public function getParsedFormConfigVerify();
}
