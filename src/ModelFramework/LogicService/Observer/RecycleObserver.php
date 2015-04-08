<?php
/**
 * Class RecycleObserver
 * @package ModelFramework\ModelViewService
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\LogicService\Logic;


class RecycleObserver implements \SplObserver
{

    /**
     * @param \SplSubject|Logic $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {

        $models = $subject->getEventObject();
        $fs = $subject->getFilesystemServiceVerify();

        foreach ($models as $id => $model) {
            $results[$id] = $fs->deleteFile($model->document());
        }

        return $results;
    }
}
