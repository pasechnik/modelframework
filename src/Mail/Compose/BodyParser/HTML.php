<?php
/**
 * Created by PhpStorm.
 * User: ksv
 * Date: 7/11/14
 * Time: 5:42 PM
 */

namespace Mail\Compose\BodyParser;

use Zend\Mail\Headers;

class HTML implements ParserInterface
{

    protected $additionalCSSWord = [
        "<link href='/css/themes/view/mail.css' media='screen' rel='stylesheet' type='text/css'>",
        "<link href='/css/themes/view/font.css' media='screen' rel='stylesheet' type='text/css'>",
    ];

    /**
     * parsing text
     *
     * @param string  $data
     * @param Headers $header           order witch parts should be seen
     * @param Array   $additionalParams array of additional params
     *
     * @return Array
     */
    public function parse( $data, $header, $additionalParams = null )
    {
        //remove links
        $data = preg_replace( '/<link(.*?)(\\/|\\\\)>/is', '', $data );
        $data = preg_replace( '/<link(.*?)>/is', '', $data );
        $data = preg_replace( '/<script(.*?)>(.*?)<(\\/|\\\\)script>/is', '', $data );
        $data = preg_replace( '/<script(.*?)>/is', '', $data );

        //remove inline scripts
        $data = preg_replace( '/<script>(.*)/is', '', $data );

        //remove frameset and frame tags
        $data = preg_replace( '/<iframe>(.*?)<\/iframe>/is', '', $data );
        $data = preg_replace( '/<frameset>(.*?)<\/frameset>/is', '', $data );
        $data = preg_replace( '/<frameset>(.*)/is', '', $data );
        $data = preg_replace( '/<frame(.*?)>/is', '', $data );

        //prepare body to display
        $data = trim( preg_replace( '/\s+/', ' ', $data ) );

        preg_match( '/<html.*>.*(<body>.*<\/body>).*<\/html>/is', $data, $res );
        if (!count( $res )) {
            $header = $header->get( 'ContentType' );
            $header = $header ? trim( preg_replace( '/\s+/', ' ', $header->toString() ) ) : '';
            $header = "<meta http-equiv='content-type' content='$header'>";
            foreach ($this->additionalCSSWord as $css) {
                $header = $header.$css;
            }

            $header = "<head>$header</head>";
//            $header = "<head><meta http-equiv="content-type" content="text/html; charset=UTF-8"></head>";
            $data = "<!DOCTYPE html><html>$header<body>$data</body></html>";
        }

        return $data;
    }
}
