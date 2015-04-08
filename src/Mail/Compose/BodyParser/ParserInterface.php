<?php
/**
 * Created by PhpStorm.
 * User: ksv
 * Date: 7/11/14
 * Time: 5:11 PM
 */

namespace Mail\Compose\BodyParser;

use Zend\Mail\Headers;

interface ParserInterface
{
    /**
     * parsing text
     *
     * @param string  $data
     * @param Headers $header           order witch parts should be seen
     * @param Array   $additionalParams array of additional params
     *
     * @return Array
     */
    public function parse($data, $header, $additionalParams = null);
}
