<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\FormService\FormConfigParser;

interface FormConfigParserAwareInterface
{
    /**
     * @param array $formConfig
     *
     * @return $this
     */
    public function setFormConfigParser(FormConfigParser $formConfigParser);

    /**
     * @return array
     */
    public function getFormConfigParser();

    /**
     * @return array
     * @throws \Exception
     */
    public function getFormConfigParserVerify();
}
