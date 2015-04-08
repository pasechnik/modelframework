<?php

namespace Mail\Compose;

use Zend\Mime\Message;
use Zend\Mime\Mime;

/**
 * Description of ComposeStrategyInterface
 *
 * Converts email from \\Wepo\\Lib\\Mail\\MailPart format to
 * data array format and back.
 *
 * @author KSV
 */
interface ComposeStrategyInterface
{
    /**
     * carves mail data by tags and returns
     * tagged array
     *
     * @param MailPart $mail
     *
     * return mail object of type you wish
     * @return Array
     */
    public function carveData(MailPart $mail);

    /**
     * pack tagged array of data to mime mail
     *
     *
     * @param Array $mail
     *
     * return array of data that will be converted to
     * send message
     * @return Array
     */
    public function packData($mail);
}
