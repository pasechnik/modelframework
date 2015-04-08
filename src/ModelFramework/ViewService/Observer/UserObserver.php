<?php
/**
 * Class ViewObserver
 * @package ModelFramework\ViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ViewService\View;

class UserObserver
    implements \SplObserver
{
    public function update(\SplSubject $subject)
    {
        /**
         * @var View $subject
         */
        $result              = [ ];
        $model               = $subject->getUser();
        if (!$model) {
            throw new \Exception('User not found');
        }
        $result[ 'model' ]          = $model;
        $result[ 'title' ]          = $subject->getViewConfigVerify()->title.' '.$model->title;
//        $this->widgets( $subject, $model );
        $subject->setData($result);
    }
}
