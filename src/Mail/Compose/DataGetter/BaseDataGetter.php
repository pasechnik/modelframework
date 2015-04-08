<?php
/**
 * Created by PhpStorm.
 * User: ksv
 * Date: 7/17/14
 * Time: 8:44 PM
 */

namespace Mail\Compose\DataGetter;

abstract class BaseDataGetter
{
    protected $tag = null;

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
     * @param Array  $header
     *
     * @return Object
     */
    abstract public function fetchData($content, $header);
}
