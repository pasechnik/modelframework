<?php
/**
 * Created by PhpStorm.
 * User: ksv
 * Date: 7/11/14
 * Time: 5:42 PM
 */

namespace Mail\Compose\BodyParser;

use Zend\Mail\Headers;

class Text implements ParserInterface
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

    protected $additionalCSSWord = [
        "<link href='/css/themes/view/mail.css' media='screen' rel='stylesheet' type='text/css'>",
        "<link href='/css/themes/view/font.css' media='screen' rel='stylesheet' type='text/css'>",
    ];

    public function parse($data, $header, $additionalParams = null)
    {
        // TODO: Implement parse() method.

        $header = '';
        foreach ($this->additionalCSSWord as $css) {
            $header = $header.$css;
        }

        $data = "<!DOCTYPE html><html><head>$header</head><body><div class='data'>$data</div></body></html>";

        return $data;
    }
}
