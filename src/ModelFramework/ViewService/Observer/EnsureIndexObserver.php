<?php
/**
 * Class FieldObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;
use ModelFramework\ViewService\View;
use ModelFramework\ViewService\ViewConfig\ViewConfig;

class EnsureIndexObserver
    implements \SplObserver, SubjectAwareInterface
{

    use SubjectAwareTrait;

    /**
     * @param \SplSubject|View $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $count = 0;
        $indexed = [];
        $models  = [];
        $this->setSubject($subject);
        $data    = $subject->getParam('data', null);
        $checked = $subject->getParam('checked', []);
        foreach (
            $subject->getModelServiceVerify()
                ->getAllModelNames() as $model
        ) {
            $_check = in_array($model, $checked) || $data == $model;
            if ($_check) {
                $count++;
            }
            $models[] = [
                'name'    => $model,
                'checked' => $_check
            ];

            if ($_check) {
                $indexed[] = $model;
            }
        }
        $this->makeIndex($indexed);

        if ($count >= count($models)) {
            $data = 'all';
        }
        $subject->setData([
            'models' => $models,
            'all'    => $data == 'All',
            'data'   => $data,
            'count'  => $count,
        ]);

        return;
    }


    protected function makeIndex($models)
    {
        /** @var View $subject */
        $subject = $this->getSubject();
        $subject->getModelServiceVerify()
            ->setGatewayService($subject->getGatewayServiceVerify());
        foreach ($models as $model) {
            $subject->getModelServiceVerify()->makeIndexes($model);
        }
        return;

    }

}
