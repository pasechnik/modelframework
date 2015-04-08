<?php

namespace Mail\Compose;

use Zend\Mail\Header\MessageId;
use Zend\Mail\Storage\Message as ReceiveMessage;
use Zend\Mail\Message as SendMessage;

/**
 * Description of MailConvert
 *
 * Converts email from send format to
 * internal format and back.
 * Checking transfer encoding and charset
 * encoding and convert to
 *
 * @author KSV
 */
class MailConvert
{

    const AttachmentFiles = 'FILES';
    const AttachmentInfo = 'INFO';
    const AttachmentNone = 'NONE';

    private static $attachmentTypes
        = [
            'application',
            'image',
            'audio',
            'video'
        ];

    protected static $BodyParsers
        = [
            'File'       => 'Mail\\Compose\\BodyParser\\File',
            'HTML'       => 'Mail\\Compose\\BodyParser\\HTML',
            'RTF'        => 'Mail\\Compose\\BodyParser\\RTF',
            'StrongText' => 'Mail\\Compose\\BodyParser\\StrongText',
            'Text'       => 'Mail\\Compose\\BodyParser\\Text',
        ];

    protected static $PartIterators
        = [
            'BaseIterator' => 'Mail\\Compose\\PartIterator\\BaseIterator'
        ];

//    protected static $Tags = [
//        MailPart::DATA_PART_TYPE => [
//
//        ],
//
//        MailPart::COMBINER_PART_TYPE => [],
//
//        'common' => [
//            'header' => [
//                'data_getter'       => 'Mail\\Compose\\DataGetter\\HeaderDataGetter',
//                'data_uniter'       => 'Mail\\Compose\\DataUniter\\HeaderUniter',
//                'data_configurator' => 'Mail\\Compose\\DataConfigurator\\HeaderConfigurator'
//            ],
//            'text' => [
//                'data_getter'       => 'Mail\\Compose\\DataGetter\\TextDataGetter',
//                'data_uniter'       => 'Mail\\Compose\\DataUniter\\BaseUniter',
//                'data_configurator' => 'Mail\\Compose\\DataConfigurator\\TextConfigurator'
//            ],
//
//            'info' => [
//                'data_getter'       => 'Mail\\Compose\\DataGetter\\TextDataGetter',
//                'data_uniter'       => 'Mail\\Compose\\DataUniter\\BaseUniter',
//                'data_configurator' => 'Mail\\Compose\\DataConfigurator\\BaseConfigurator'
//            ],
//
//            'link' => [
//                'data_getter'       => 'Mail\\Compose\\DataGetter\\AttachmentDataGetter',
//                'data_uniter'       => 'Mail\\Compose\\DataUniter\\BaseUniter',
//                'data_configurator' => 'Mail\\Compose\\DataConfigurator\\BaseConfigurator'
//            ]
//        ]
//    ];

    protected static $Tags
        = [
            'header' => [
                'data_getter'       => [
                    'part_type' => MailPart::COMMON_PART_TYPE,
                    'class'     => 'Mail\\Compose\\DataGetter\\HeaderDataGetter'
                ],
                'data_uniter'       => 'Mail\\Compose\\DataUniter\\HeaderUniter',
                'data_configurator' => 'Mail\\Compose\\DataConfigurator\\HeaderConfigurator'
            ],
            'text'   => [
                'data_getter'       => [
                    'part_type' => MailPart::DATA_PART_TYPE,
                    'class'     => 'Mail\\Compose\\DataGetter\\TextDataGetter'
                ],
                'data_uniter'       => 'Mail\\Compose\\DataUniter\\BaseUniter',
                'data_configurator' => 'Mail\\Compose\\DataConfigurator\\TextConfigurator'
            ],
            'info'   => [
                'data_getter'       => [
                    'part_type' => MailPart::DATA_PART_TYPE,
                    'class'     => 'Mail\\Compose\\DataGetter\\TextDataGetter'
                ],
                'data_uniter'       => 'Mail\\Compose\\DataUniter\\BaseUniter',
                'data_configurator' => 'Mail\\Compose\\DataConfigurator\\BaseConfigurator'
            ],
            'link'   => [
                'data_getter'       => [
                    'part_type' => MailPart::DATA_PART_TYPE,
                    'class'     => 'Mail\\Compose\\DataGetter\\AttachmentDataGetter'
                ],
                'data_uniter'       => 'Mail\\Compose\\DataUniter\\BaseUniter',
                'data_configurator' => 'Mail\\Compose\\DataConfigurator\\BaseConfigurator'
            ]
        ];

    protected static $typeSettings
        = [
            'text'        => [
                'plain'    => [
                    'type'      => MailPart::DATA_PART_TYPE,
                    'parser'    => [
                        'Text'
                    ],
                    'data_tags' => [
                        'text',
                        'header'
                    ],
                ],
                'html'     => [
                    'type'      => MailPart::DATA_PART_TYPE,
                    'parser'    => [
                        'HTML'
                    ],
                    'data_tags' => [
                        'text',
                        'header'
                    ],
                ],
                'richtext' => [
                    'type'      => MailPart::DATA_PART_TYPE,
                    'parser'    => [
                        'RTF'
                    ],
                    'data_tags' => [
                        'text',
                        'header'
                    ],
                ],
                'default'  => [
                    'type'      => MailPart::DATA_PART_TYPE,
                    'parser'    => [
                        'StrongText'
                    ],
                    'data_tags' => [
                        'text',
                        'header'
                    ],
                ]
            ],
            'message'     => [
                'rfc822'          => [
                    'type'      => MailPart::DATA_PART_TYPE,
                    'data_tags' => [],
                    'parser'    => []
                ],
                'delivery-status' => [
                    'type'      => MailPart::DATA_PART_TYPE,
                    'parser'    => [
                        'StrongText'
                    ],
                    'data_tags' => [
                        'info'
                    ],
                ],
                'default'         => [
                    'type'      => MailPart::DATA_PART_TYPE,
                    'data_tags' => [
                        'info'
                    ],
                ]
            ],
            'multipart'   => [
                'alternative' => [
                    'type'      => MailPart::COMBINER_PART_TYPE,
                    'iterator'  => [
                        'BaseIterator' => [
                            'order' => 'desc',
                            'count' => 1,
                        ]
                    ],
                    'data_tags' => [
                        'text',
                        'info',
                        'header'
                    ]
                ],
                'related'     => [
                    'type'      => MailPart::COMBINER_PART_TYPE,
                    'iterator'  => [
                        'BaseIterator' => [
                            'order' => 'asc',
                            'count' => 0,
                        ]
                    ],
                    'data_tags' => [
                        'text',
                        'info',
                        //                        'link',
                        'header'
                    ]
                ],
                'mixed'       => [
                    'type'      => MailPart::COMBINER_PART_TYPE,
                    'iterator'  => [
                        'BaseIterator' => [
                            'order' => 'asc',
                            'count' => 0,
                        ]
                    ],
                    'data_tags' => [
                        'text',
                        'info',
                        //                        'link',
                        'header'
                    ]
                ],
                'report'      => [
                    'type'      => MailPart::COMBINER_PART_TYPE,
                    'iterator'  => [
                        'BaseIterator' => [
                            'order' => 'asc',
                            'count' => 0
                        ]
                    ],
                    'data_tags' => [
                        'text',
                        'info',
                        //                        'link',
                        'header'
                    ]
                ],
                'parallel'    => [
                    'type'      => MailPart::COMBINER_PART_TYPE,
                    'iterator'  => [
                        'BaseIterator' => [
                            'order' => 'asc',
                            'count' => 0
                        ]
                    ],
                    'data_tags' => [
                        'text',
                        'info'
                    ]
                ],
            ],
            'audio'       => [
                'default' => [
                    'type'      => MailPart::DATA_PART_TYPE,
                    'parser'    => [
                        'File'
                    ],
                    'data_tags' => [
                        //                        'link'
                    ],
                ],
            ],
            'video'       => [
                'default' => [
                    'type'      => MailPart::DATA_PART_TYPE,
                    'parser'    => [
                        'File'
                    ],
                    'data_tags' => [
                        //                        'link'
                    ],
                ]
            ],
            'image'       => [
                'default' => [
                    'type'      => MailPart::DATA_PART_TYPE,
                    'parser'    => [
                        'File'
                    ],
                    'data_tags' => [
                        //                        'link'
                    ],
                ]
            ],
            'application' => [
                'default' => [
                    'type'      => MailPart::DATA_PART_TYPE,
                    'parser'    => [
                        'File'
                    ],
                    'data_tags' => [
                        //                        'link'
                    ],
                ]
            ],
        ];


    public static function  getConfigurators()
    {
        $ret = [];
//        foreach(static::$Tags as $tagsArr)
//        {
//            foreach($tagsArr as $tag=>$config)
//            {
//                $ret[$tag] = new $config['data_configurator']();
//            }
//        }
        foreach (static::$Tags as $tag => $config) {
            $ret[$tag] = new $config['data_configurator']();
        }

        return $ret;
    }


    private $composeMailStrategy = null;
    private $attachmentProcessingType = self::AttachmentFiles;

    /**
     * Construct method, require strategy to compose or decompose mail
     * to internal format
     *
     * @param ComposeStrategyInterface $composeStrategy
     */
    public function __construct(ComposeStrategyInterface $composeStrategy)
    {
        $this->composeMailStrategy = $composeStrategy;
    }


    /**
     * @param AttachmentFiles|AttachmentInfo|AttachmentNone $attachmentProcessingType
     *
     * @throws
     */
    public function setAttachmentProcessingType($attachmentProcessingType)
    {
        if ( !in_array($attachmentProcessingType,
            [self::AttachmentInfo, self::AttachmentFiles, self::AttachmentNone])
        ) {
            throw new \Exception('Wrong attachment processing type');
        }
        $this->attachmentProcessingType = $attachmentProcessingType;
    }

    /**
     * convert raw mail to format you want to get.
     * Format convert made by strategy that corresponds to MailComposeInterface
     *
     * @param ReceiveMessage $rawMail
     *
     * mail and array of attachment links
     *
     * @return Array
     */
    public function convertMailToInternalFormat(ReceiveMessage $rawMail)
    {
//        prn('before');
        $parts = $this->parseMailParts($rawMail);
//        prn('after',$parts);
        if(isset($parts)) {
            $res = $this->composeMailStrategy->carveData( $parts );
        }
        else{
            $res = [];
        }
        if ( !isset($res['header']['message-id'])) {
            $MessageId = md5($rawMail->getHeaders()->toString()
                . $rawMail->getContent());

            if (isset($_SERVER["SERVER_NAME"])) {
                $hostName = $_SERVER["SERVER_NAME"];
            } else {
                $hostName = php_uname('NNMHost');
            }
            $res['header']['message-id']
                = '<' . $MessageId . '@' . $hostName . '>';
        }
//        prn($res);
//        exit;
        return $res;
    }


    /**
     * convert raw mail array to zf2 SendMessage format.
     * Format convert made by strategy that corresponds to ComposeStrategyInterface
     *
     * @param  array $mail
     *
     * mail prepared to send
     *
     * @return SendMessage
     */
    public function convertToSendFormat(Array $mail)
    {
        $sendMessage = $this->composeMailStrategy->packData($mail);

//        $messageID = new MessageId();
//        $messageID->setId();

//        $headers = $sendMessage->getHeaders();
//        $headers->addHeader($messageID);
//        $sendMessage->setHeaders($headers);

        return $sendMessage;
    }


    /**
     * convert raw mail to common array format.
     *
     * @param ReceiveMessage $rawMail
     *
     * common mail array format
     *
     * @return null|MailPart
     */
    private function parseMailParts(ReceiveMessage $rawMail)
    {
        $rawMailHeaders = $rawMail->getHeaders();
        $contentType    = isset($rawMail->content_type)
            ? $rawMail->getHeaderField('Content-Type') : 'no_content_type';

        $part = $this->configurePart($contentType);
        if (is_null($part)) {
//            prn('first');
            return $part;
        }

        if ($rawMail->isMultipart()) {
            $partResult = [];
            try {
                foreach ($rawMail as $rawPart) {
//                    prn($rawPart);
                    $tres = $this->parseMailParts($rawPart);
//                    prn($tres);
                    if ( !isset($tres)) {
                        continue;
                    }
                    $partResult[] = $tres;
                }
            } catch (\Zend\Mail\Header\Exception\InvalidArgumentException $exc) {
//                prn($exc->getMessage());
            }
//            prn($partResult);

            $part->setContent($partResult);
            $part->setHeaders($rawMailHeaders);
        } else {
            $isAttachment = in_array(explode('/', $contentType)[0],
                $this::$attachmentTypes);
            $partResult   = $rawMail->getContent();

            if (in_array('Content-Transfer-Encoding',
                    array_keys($rawMailHeaders->toArray()))
                && isset($partResult)
            ) {
                $partResult = $this->decode($partResult,
                    $rawMail->contentTransferEncoding);
                $rawMailHeaders->removeHeader('Content-Transfer-Encoding');
            }

            if ($isAttachment) {
                switch ($this->attachmentProcessingType) {
                    case self::AttachmentNone:
//                        prn('second');
                        return null;
                        break;
                    case self::AttachmentInfo:
                        $partResult = null;
                        break;
                    case self::AttachmentFiles:
                        break;
                }
            } else {
                try {
                    $mailCharset = $rawMail->getHeaderField('content-type',
                        'charset');
                } catch (\Exception $ex) {
                    $mailCharset = null;
                }
                $partResult = $this->setMailEncoding($partResult, $mailCharset);
            }
        }
        $part->setContent($partResult);
        $part->setHeaders($rawMailHeaders);

//        prn('third');
        return $part;
    }

    /**
     * @param string $contentType
     *
     * @return MailPart
     */
    private function configurePart($contentType)
    {
//        prn($contentType);
        $contentType = explode('/', $contentType);
        $part        = null;
        if (isset($contentType[0])
            && in_array($contentType[0], array_keys($this::$typeSettings))
        ) {
            if (isset($contentType[1])
                && (isset($this::$typeSettings[$contentType[0]][$contentType[1]]))
            ) {
                $setting
                      = $this::$typeSettings[$contentType[0]][$contentType[1]];
                $part = new MailPart($setting['type'], $contentType);
            } elseif (isset($this::$typeSettings[$contentType[0]]['default'])) {
                $setting = $this::$typeSettings[$contentType[0]]['default'];
                $part    = new MailPart($setting['type'], $contentType);
            }

            if (isset($setting)) {
                if ($setting['type'] == MailPart::DATA_PART_TYPE) {
//                    foreach($setting['data_tags'] as $tag)
//                    {
//                        $Tags = $this::$Tags;
//                        $tagSetting = $Tags[$setting['type']][$tag];
//                        $dataGetter = new $tagSetting['data_getter']( ['tag'=>$tag] );
//                        $part->addDataGetter($dataGetter);
//                    }
                    foreach ($setting['parser'] as $parser) {
                        $Parsers = self::$BodyParsers;
                        $parser  = new $Parsers[$parser]();
                        $part->addParser($parser);
                    }
                } elseif ($setting['type'] == MailPart::COMBINER_PART_TYPE) {
                    $iterator         = array_keys($setting['iterator'])[0];
                    $iteratorSettings = $setting['iterator'][$iterator];
                    $Iterators        = $this::$PartIterators;
                    $iterator
                                      = new $Iterators[$iterator]($iteratorSettings);
                    $part->setIterator($iterator);
//                    foreach($setting['data_tags'] as $tag)
//                    {
//                        $Tags = $this::$Tags;
//                        $tagSetting = $Tags[$setting['type']][$tag];
//                        $dataUniter = new $tagSetting['data_uniter']();
//                        $part->addDataUniter($tag, $dataUniter);
//                    }
                }
                foreach ($setting['data_tags'] as $tag) {
                    $Tags = $this::$Tags;
//                    prn($contentType,$Tags,$setting['type'],$tag);
//                    if(isset($Tags[$setting['type']][$tag]))
//                    {
//                        $tagSetting = $Tags[$setting['type']][$tag];
//                    }
//                    elseif(isset($Tags['common'][$tag]))
//                    {
//                        $tagSetting = $Tags['common'][$tag];
//                    }
//                    else
//                    {
//                        throw new \Exception(
//                            'Tags settings are wrong. Mail service can\'t find setting for tag '.$tag.' either in common or '.$setting['type'].' section.'
//                        );
//                    }
//                    switch($setting['type'])
//                    {
//                        case MailPart::COMBINER_PART_TYPE:
//                            $dataUniter = new $tagSetting['data_uniter']();
//                            $part->addDataUniter($tag, $dataUniter);
//                        case MailPart::DATA_PART_TYPE:
//                            $dataGetter = new $tagSetting['data_getter']( ['tag'=>$tag] );
//                            $part->addDataGetter($dataGetter);
//                            break;
//                    }
                    $tagSetting = $Tags[$tag];
                    if ($setting['type'] == MailPart::COMBINER_PART_TYPE) {
                        $dataUniter = new $tagSetting['data_uniter']();
                        $part->addDataUniter($tag, $dataUniter);
                    }
                    if (in_array($tagSetting['data_getter']['part_type'],
                        [MailPart::COMMON_PART_TYPE, $setting['type']])) {
                        $dataGetter
                            = new $tagSetting['data_getter']['class'](['tag' => $tag]);
                        $part->addDataGetter($dataGetter);
                    }

                }
            }

        }

        return $part;
    }


    /**
     * decoding text
     *
     * @param string $content          content string
     * @param string $transferEncoding encoding type string
     *
     * @return string
     */
    private function decode($content, $transferEncoding)
    {
        $transferEncoding = strtolower($transferEncoding);

        switch ($transferEncoding) {
            case 'base64':
                return base64_decode($content);
                break;
            case 'quoted-printable':
                return quoted_printable_decode($content);
                break;
            default:
                //in case 7bit, 8bit or binary
                return $content;
                break;
        }
    }

    /**
     * changing charset encoding
     *
     * @param string      $text                content string
     * @param string|null $contentTypeEncoding encoding type string
     *
     * @return string
     */
    private function setMailEncoding($text, $contentTypeEncoding = null)
    {
        // TODO: Create own method to detect text encoding
        if (isset($contentTypeEncoding)) {
            mb_detect_order([
                'UTF-8',
                'UTF-7',
                'ASCII',
                $contentTypeEncoding,
                'EUC-JP',
                'SJIS',
                'eucJP-win',
                'SJIS-win',
                'JIS',
                'ISO-2022-JP'
            ]);
        } else {
            mb_detect_order([
                'UTF-8',
                'UTF-7',
                'ASCII',
                'EUC-JP',
                'SJIS',
                'eucJP-win',
                'SJIS-win',
                'JIS',
                'ISO-2022-JP'
            ]);
        }
        $currentCharset = mb_internal_encoding();
        $textEncoding   = mb_detect_encoding($text);

        if ($currentCharset != $textEncoding) {
            if (isset($contentTypeEncoding)) {
                $text = mb_convert_encoding($text, $currentCharset,
                    $contentTypeEncoding);
            } else {
                $text = mb_convert_encoding($text, $currentCharset,
                    $textEncoding);
            }
        }
        return $text;
    }
}
