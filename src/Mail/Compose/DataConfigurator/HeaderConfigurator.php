<?php
/**
 * Created by PhpStorm.
 * User: ksv
 * Date: 8/8/14
 * Time: 6:26 PM
 */

namespace Mail\Compose\DataConfigurator;

class HeaderConfigurator extends BaseConfigurator
{
    protected $emailParseRegExp = '/\b[A-Z0-9._%+-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,6}\b/i';
    protected $messageIdParseRegExp = '/\<.*?\>/';

    protected $headersToReturn = [
        'subject',
        'from',
        'to',
        'cc',
        'bcc',
        'message-id',
        'in-reply-to',
        'references',
        'reply-to',
        'sender',
        'return-path',
        'date',
    ];

    /**
     * @param  mixed $tagData
     * @return mixed
     */
    public function configure($tagData)
    {
        $headers = $tagData;
        $newHeaders = [];
        foreach ($headers as $name => $header) {
            $checkname = strtolower(trim($name));
            if (in_array($checkname, $this->headersToReturn)) {
                switch ($checkname) {
                    case "to":
                    case "from":
                    case "bb":
                    case "cc":
                    case "reply-to":
                        preg_match_all($this->emailParseRegExp, $header, $out, PREG_PATTERN_ORDER);
                        $header = $out[0];
                        break;
                    case "references":
                        preg_match_all($this->messageIdParseRegExp, $header, $out, PREG_PATTERN_ORDER);
                        $header = $out[0];
                        break;
                }
                $newHeaders[$checkname] = $header;
            }
        }

        return $newHeaders;
    }
}
