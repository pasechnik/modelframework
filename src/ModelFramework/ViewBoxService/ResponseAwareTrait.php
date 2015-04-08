<?php

namespace ModelFramework\ViewBoxService;

trait ResponseAwareTrait
{
    /**
     * @var null
     */
    private $_response = null;

    /**
     * @param $response
     */
    public function setResponse($response)
    {
        $this->_response = $response;
    }

    /**
     * @return null
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        if (!empty($this->_response)) {
            return true;
        }

        return false;
    }
}
