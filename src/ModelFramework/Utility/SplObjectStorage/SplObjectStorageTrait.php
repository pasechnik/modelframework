<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:32
 */

namespace ModelFramework\Utility\SplObjectStorage;

trait SplObjectStorageTrait
{

    private $_storage = null;

    public function initSplObjectStorage()
    {
        if ($this->_storage === null) {
            $this->_storage = new \SplObjectStorage();
        }

        return $this->_storage;
    }

    public function getSplObjectStorage()
    {
        return $this->initSplObjectStorage();
    }

    public function emptySplObjectStorage()
    {
        return $this->_storage = null;
    }

}
