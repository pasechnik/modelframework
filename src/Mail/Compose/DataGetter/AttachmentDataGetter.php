<?php
/**
 * Created by PhpStorm.
 * User: ksv
 * Date: 7/17/14
 * Time: 8:44 PM
 */

namespace Mail\Compose\DataGetter;

class AttachmentDataGetter extends BaseDataGetter
{
    public function __construct($params)
    {
        if (!in_array('tag', array_keys($params))) {
            throw new \Exception('Wrong data getter configuration');
        }
        $this->tag = $params['tag'];
    }

    /**
     * returns teg that will be associated to returned data
     *
     * @return Object
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * get attachments from common mail array if exists
     *
     * @param string $content
     * @param string $header
     *
     * @return Object
     */
    public function fetchData($content, $header)
    {
        //        prn(getcwd());
//
//        prn($header->toArray());
//        prn($mailArray['body']);

//        $data = $mailArray['body'];
        $fileName = $header->get('content-type')->getParameter('name');
//        prn($fileName);
//        $file = fopen($fileName, 'w');
//        fwrite($file, $content);
//        fclose($file);

//        return [ $fileName => $content ];
        return [ $fileName => '' ];
    }
}
