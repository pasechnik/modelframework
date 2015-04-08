<?php
/**
 * Class PermissionObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

class PermissionObserver extends AbstractObserver
{

    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );
        $where = [ ];
        $user  = $subject->getAuthServiceVerify()->getUser();
        foreach ($this->getRootConfig() as $field => $value) {
            if ($user->role_id == $value) {
                $where[ $field ] = $user->id();
            }
        }
        $subject->setWhere( $where );
    }

    /*
     *
     * db.Lead.find( { "acl":{ $elemMatch:  {"name" : { $in: ["Users", "Admins", "User_1253216378126371"] }, "permissions": { $in: ['c', 'r'] } } } } )
     *
     */
}
