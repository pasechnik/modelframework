<?php
/**
 * Class IsDefaultObserver
 * @description устанавливает в 'false' поле $key всем экземплярам модели типа $value
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class IsDefaultObserver extends AbstractConfigObserver
{
    use SubjectAwareTrait;

    public function process($model, $key, $value)
    {

        if ($model->$key == 'false') {
            return;
        }

        $this->getSubject()->getGatewayService()->get($model->getModelName())
            ->update([$key => 'false'], [$key => $model->$key, $value => $model->$value, '-_id' => $model->id()]);
        return;
    }
}
