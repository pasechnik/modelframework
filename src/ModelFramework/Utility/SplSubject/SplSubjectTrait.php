<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:25
 */

namespace ModelFramework\Utility\SplSubject;

use ModelFramework\Utility\SplObjectStorage\SplObjectStorageTrait;

trait SplSubjectTrait
{
    use SplObjectStorageTrait;

    public function attach(\SplObserver $observer)
    {
        $this->getSplObjectStorage()->attach($observer);
    }

    public function detach(\SplObserver $observer)
    {
        $this->getSplObjectStorage()->detach($observer);
    }

    public function notify()
    {
        foreach ($this->getSplObjectStorage() as $observer) {
            $observer->update($this);
        }
    }

}
