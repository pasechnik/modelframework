<?php

namespace Mail\Compose;

use Zend\Mail\Message as SendMessage;
use Zend\Mime\Message;
use Zend\Mime\Mime;
use Zend\Mime\Part;

/**
 * Description of MailConvert
 *
 * Converts email from send format to
 * internal format and back.
 *
 * @author KSV
 */
class DefaultComposeStrategy implements ComposeStrategyInterface
{

    protected $attachmentContentTypes = [
        'pdf'  => 'application/pdf',
        'rar'  => 'application/rar',
        'mp4'  => 'audio/mp4',
        'mp3'  => 'audio/mpeg',
        'zip'  => 'application/zip',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'ppt'  => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];

    /**
     * carves mail data by tags and returns
     * tagged array
     *
     * @param MailPart $mail
     *
     * return mail object of type you wish
     *
     * @throws \Exception
     * @return Array
     */
    public function carveData( MailPart $mail )
    {
        //        prn($mail->dataGetters);
//        prn($mail);
//        prn($mail->dataUniters);
//        exit;
        $resArray = [ ];
        if (isset( $mail )) {
            $resArray      = $mail->getData();
            $configurators = [ ];
            if (is_array( $resArray )) {
                $configurators = MailConvert::getConfigurators();
            }
            foreach ($resArray as $tag => $part) {
                //                prn($tag,$resArray[$tag]);
                try {
                    $resArray[ $tag ] =
                        $configurators[ $tag ]->configure( $resArray[ $tag ] );
                } catch ( \Exception $ex ) {
                    $exceptionMessage = isset( $configurators[ $tag ] ) ?
                        'Internal error in ' .
                        get_class( $configurators[ $tag ] ) .
                        ' configurator for mail tag ' . $tag :
                        'Configurator for tag ' . $tag . ' doesn\'t set';
                    throw new \Exception( $exceptionMessage );
                }
            }
        }
//        prn($resArray);
//        exit;

        return $resArray;
    }

    /**
     * pack tagged array of data to SendMessage format
     *
     *
     * @param Array $mailArray
     *
     * return array of data that will be converted to
     * send message
     *
     * @return Array
     */
    public function packData( $mailArray )
    {
        $mimeMail = new Message();
        $textPart = $this->packText( $mailArray[ 'text' ],
            $mailArray[ 'header' ][ 'content-type' ] );
        unset( $mailArray[ 'header' ][ 'content-type' ] );
        $attachmentParts = $this->packAttachments( $mailArray[ 'link' ] );
        if (count( $attachmentParts )) {
            $mimeMail->addPart( $textPart );
            foreach ($attachmentParts as $part) {
                $mimeMail->addPart( $part );
            }
        } else {
            $mimeMail->addPart( $textPart );
        }
        $returnMail = new SendMessage();
        $returnMail->setBody( $mimeMail );

        foreach ($mailArray[ 'header' ] as $header => $value) {
            switch ($header) {
                case 'references' :
                    if (is_array( $value )) {
                        $res = '';
                        foreach ($value as $reference) {
                            $res .= $reference . ' ';
                        }
                    } elseif (is_string( $value )) {
                        $res = $value;
                    } else {
                        continue;
                    }
                    $headers = $returnMail->getHeaders();
                    $headers->addHeaderLine( $header, $res );
                    $returnMail->setHeaders( $headers );
                    break;
                case 'bcc':
                    $returnMail->addBcc( $value );
                    break;
                case 'cc':
                    $returnMail->addCc( $value );
                    break;
                case 'to':
                    $returnMail->addTo( $value );
                    break;
                case 'from':
                    $returnMail->addFrom( $value );
                    break;
                case 'reply-to':
                    $returnMail->addReplyTo( $value );
                    break;
                case 'subject':
                    $returnMail->setSubject( $value );
                    break;
                default:
                    $headers = $returnMail->getHeaders();
                    $headers->addHeaderLine( $header, $value );
                    $returnMail->setHeaders( $headers );
                    break;
            }
        }

        return $returnMail;
    }

    /**
     * check if text is html type
     *
     * @param string $text
     *
     * @return bool
     */
    public function isHTML( $text )
    {
        $tempText = strip_tags( $text );
        if ($text == $tempText) {
            return false;
        }

        return true;
    }

///////////////////////////////////////Data Packers//////////////////////////////////////

    protected function packAttachments( $attachment )
    {
        $parts = [ ];
        if (is_array( $attachment )) {
            foreach ($attachment as $name => $content) {
                $ext               = explode( '.', $name );
                $ext               = $ext[ count( $ext ) - 1 ];
                $contentType       =
                    isset( $this->attachmentContentTypes[ $ext ] ) ?
                        $this->attachmentContentTypes[ $ext ] :
                        Mime::TYPE_OCTETSTREAM;
                $part              = new Part( $content );
                $part->type        = $contentType . '; name=' . $name;
                $part->disposition = 'attachment; filename=' . $name;
                $part->encoding    = 'base64';
                $parts[ ]          = $part;
            }
        }

        return $parts;
    }

    protected function packText( $text, $contentType )
    {
        if ($contentType == 'text/html') {
            $part          = new Part( $text );
            $part->type    = Mime::TYPE_HTML;
            $part->charset = mb_internal_encoding();
        } else {
            $part          = new Part( $text );
            $part->type    = Mime::TYPE_TEXT;
            $part->charset = mb_internal_encoding();
        }

        return $part;
    }
}
