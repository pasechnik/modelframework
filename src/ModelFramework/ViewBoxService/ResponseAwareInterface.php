<?php
/**
 * Created by PhpStorm.
 * User: prog4
 * Date: 12/25/14
 * Time: 5:15 PM
 */

namespace ModelFramework\ViewBoxService;

interface ResponseAwareInterface
{
    /**
     * @param $response
     */
    public function setResponse($response);

    /**
     * @return null
     */
    public function getResponse();

    /**
     * @return bool
     */
    public function hasResponse();
}
