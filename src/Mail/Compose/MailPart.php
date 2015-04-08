<?php
/**
 * Created by PhpStorm.
 * User: ksv
 * Date: 7/17/14
 * Time: 8:15 PM
 */

namespace Mail\Compose;

use Mail\Compose\BodyParser\ParserInterface;
use Mail\Compose\DataGetter\BaseDataGetter;
use Mail\Compose\DataUniter\BaseUniter;
use Mail\Compose\PartIterator\BaseIterator;
use Zend\Mail\Headers;

class MailPart
{
    const DATA_PART_TYPE = 'DATA_PART';
    const COMBINER_PART_TYPE = 'COMBINTER_PART';
    const COMMON_PART_TYPE = 'COMMON_PART';

    public $type = null;

    public $content = null;

    public $contentType = null;

    /**
     * @var Headers
     */
    public $headers = null;

    /**
     * @var BaseIterator
     */
    public $iterator = null;

    /**
     * @var array
     */
    public $dataUniters = [];

    /**
     * @var array
     */
    public $parsers = [];

    /**
     * @var array
     */
    public $dataGetters = [];

    public function __construct($partType, $contentType)
    {
        if (!in_array($partType, [self::DATA_PART_TYPE, self::COMBINER_PART_TYPE])) {
            throw new \Exception('Wrong mail part type. '.$partType.' is not defined');
        }
        $this->type = $partType;
        $this->contentType = $contentType;
    }

    /**
     * @param BaseIterator $iterator
     */
    public function setIterator(BaseIterator $iterator)
    {
        if (!$this->type == self::COMBINER_PART_TYPE) {
            trigger_error('wrong type, your mail service settings should be wrong', E_USER_WARNING);

            return;
        }
        $this->iterator = $iterator;
    }

    /**
     * @param ParserInterface $parser
     */
    public function addParser(ParserInterface $parser)
    {
        if (!$this->type == self::DATA_PART_TYPE) {
            trigger_error('wrong type, your mail service settings should be wrong', E_USER_WARNING);

            return;
        }
        $this->parsers[] = $parser;
    }

    /**
     * @param BaseDataGetter $dataGetter
     */
    public function addDataGetter(BaseDataGetter $dataGetter)
    {
        //        if(!$this->type==self::DATA_PART_TYPE)
//        {
//            trigger_error('wrong type, your mail service settings should be wrong', E_USER_WARNING);
//            return;
//        }
        $this->dataGetters[] = $dataGetter;
    }

    /**
     * @param string     $tag
     * @param BaseUniter $dataUniter
     */
    public function addDataUniter($tag, BaseUniter $dataUniter)
    {
        if (!$this->type == self::COMBINER_PART_TYPE) {
            trigger_error('wrong type, your mail service settings should be wrong', E_USER_WARNING);

            return;
        }
        $this->dataUniters[$tag] = $dataUniter;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $resArray = [];
        $data = $this->content;
//        prn($this->type,$this->content,$this->contentType);
        if ($this->type == self::DATA_PART_TYPE) {
            foreach ($this->parsers as $parser) {
                $data = $parser->parse($data, $this->headers);
            }
        }
        foreach ($this->dataGetters as $dataGetter) {
            $resArray[$dataGetter->getTag()] = $dataGetter->fetchData($data, $this->headers);
        }
        if ($this->type == self::COMBINER_PART_TYPE) {
            $childPartData = $this->uniteData($this->iterator->fetchData($this->content));
            $resArray = count($resArray) ? $this->uniteData([ $childPartData, $resArray ]) : $childPartData;
        }

        return $resArray;
    }

    protected function uniteData($data)
    {
//                prn('part uniter', $data);
        $validKeys = array_keys($this->dataUniters);
//        prn('valid uniters keys',$validKeys);
        $result = [];
        foreach ($data as $array) {
            foreach ($array as $tag => $newData) {
                if (in_array($tag, $validKeys)) {
                    $oldData = isset($result[$tag]) ? $result[$tag] : [];
                    $newData = is_array($newData) ? $newData : [$newData];
                    $result[$tag] = $this->dataUniters[$tag]->uniteData($newData, $oldData);
                }
            }
        }

        return $result;
    }

    /**
     * @param Array|string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return Headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
