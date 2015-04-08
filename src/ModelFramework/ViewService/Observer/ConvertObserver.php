<?php
/**
 * Class ConvertObserver
 * @package ModelFramework\ViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\Utility\Arr;

class ConvertObserver extends AbstractObserver
{
    public function process($aclModel)
    {
        $model      = $this->getModelData();
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        $logic      = $subject->getLogicServiceVerify()
                              ->get('convert', $model->getModelName());
        if ($subject->getParamsVerify()->fromPost('object_id', null) !== null
        ) {
            $logic->setData([ 'save' => true ]);
        }
        $logic->trigger($model);
        if (Arr::getDoubtField($logic->getData(), 'save', false)) {
            $url = $subject->getBackUrl();
            if ($url == null || $url == '/') {
                $url = $subject->getParams()->getController()->url()
                               ->fromRoute('common', [
                                   'data' => strtolower($viewConfig->model),
                                   'view' => 'list'
                               ]);
            }
            $subject->setRedirect($subject->refresh($model->getModelName() .
                                                      ' data was successfully converted',
                $url));
        }
    }
}
