<?php
/**
 * Class AclObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

class AclObserver extends AbstractObserver
{

    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );
        $user   = $subject->getAuthServiceVerify()->getUser();
        $where  = [ ];
        $match  = [
            'data' => $this->getConfigPart( 'data' ),
        ];
        if (!count( $this->getConfigPart( 'type' ) )) {
            $match[ 'type' ]    = [ 'owner', 'shared', 'hierarchy' ];
            $match[ 'role_id' ] = [ $user->id(), $user->role_id ];
        }
        if (in_array( 'owner', $this->getConfigPart( 'type' ) )) {
            $match[ 'type' ][ ]    = 'owner';
            $match[ 'role_id' ][ ] = $user->id();
        }
        if (in_array( 'hierarchy', $this->getConfigPart( 'type' ) )) {
            $match[ 'type' ][ ]    = 'hierarchy';
            $match[ 'role_id' ][ ] = $user->role_id();
        }
        if (in_array( 'shared', $this->getConfigPart( 'type' ) )) {
            $match[ 'type' ][ ] = 'shared';
            $match[ 'role_id' ] = [ $user->id(), $user->role_id ];
        }
        $user             = $subject->getAuthServiceVerify()->getUser();
        $where [ '_acl' ] = [ '$elemMatch' => $match ];

        $subject->setWhere( $where );
    }

}
