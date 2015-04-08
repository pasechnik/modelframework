<?php
/**
 * Class LetterParamObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

class CurrentUserObserver extends AbstractObserver
{
    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);

        $data = [
            'params' => [],
        ];

        $user = $subject->getAuthServiceVerify()->getUser();

        $where = [ ];

        foreach ($this->getRootConfig() as $field => $param) {
            $where[$field] = $user->$param;
        }

        $subject->setData($data);
        $subject->setWhere($where);
    }
}
