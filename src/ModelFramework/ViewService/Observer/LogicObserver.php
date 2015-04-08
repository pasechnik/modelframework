<?php
/**
 * Class FormObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

class LogicObserver
    implements \SplObserver
{
    public function update(\SplSubject $subject)
    {
        $viewConfig = $subject->getViewConfigVerify();

        $query =
            $subject->getQueryServiceVerify()
                    ->get($viewConfig->query)
                    ->setParams($subject->getParams())
                    ->process();

        $models = $subject->getGateway()->find($query->getWhere());
        foreach ($models as $model) {
            $subject->getLogicServiceVerify()->get($viewConfig->mode, $viewConfig->model)->trigger($model->getDataModel());
        }
        $subject->setRedirect($subject->refresh($viewConfig->title.' successfull', '/common/mail/index.html'));
    }
}
