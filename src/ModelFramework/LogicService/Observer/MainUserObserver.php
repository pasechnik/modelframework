<?php
/**
 * Class AgeObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class MainUserObserver
    implements \SplObserver
{
    /**
     * @param \SplSubject|Logic $subject
     */
    public function update(\SplSubject $subject)
    {
        $this->setMainUser($subject);
    }

    //creates new MainUser if it does not exist or update old one if MainUser already exist
    public function setMainUser($subject)
    {
        $mainUserGW = $subject->getGatewayService()->get('MainUser');
        $userGW = $subject->getGatewayService()->get('User');
        $user = $subject->getEventObject();

        $oldMainUser = $mainUserGW->find(['$or' => [['_id' => $user->main_id], ['login' => $user->login]]])->current();
        $mainUser = $subject->getModelService()->get('MainUser');
        $mainUser->exchangeArray($user->toArray());
        if (isset($oldMainUser)) {
            $mainUserId = (string) $oldMainUser->_id;
            $mainUser->_id = $mainUserId;
            $mainUser->company_id = $oldMainUser->company_id;
        } else {
            $mainUserId = null;
            $mainUser->company_id = $subject->getAuthService()->getMainUser()->company_id;
        }
        $mainUserGW->save($mainUser);
        $mainUserId = empty($mainUserId) ? $mainUserGW->getLastInsertId() : $mainUserId;
        $user->main_id = $mainUserId;
        $userGW->save($user);
    }
}
