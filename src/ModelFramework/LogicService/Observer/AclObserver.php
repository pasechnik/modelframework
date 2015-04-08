<?php
/**
 * Class AclObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class AclObserver extends AbstractConfigObserver
{

    public function process( $model, $key, $value )
    {
        $user = $this->getSubject()->getAuthService()->getUser();
        $acl  = $model->_acl;
        foreach ($acl as $_key => $_aclArray) {
            if ($_aclArray[ 'type' ] == 'owner' ||
                $_aclArray[ 'type' ] == 'hierarchy'
            ) {
                unset( $acl[ $_key ] );
            }
        }
        if (isset( $value[ 'owner' ] )) {
            $acl[ ] = [
                'type'    => 'owner',
                'role'    => 'owner',
                'role_id' => !empty( $model->owner_id ) ? $model->owner_id :
                    $user->id(),
                'data'    => $value[ 'owner' ],
            ];
        }
        if (isset( $value[ 'hierarchy' ] )) {
            foreach ($value[ 'hierarchy' ] as $_key => $_value) {
                $roleClass = 'Wepo\Model\Role';
                $ucRole    = strtoupper( $_key );
                $acl[ ]    = [
                    'type'    => 'hierarchy',
                    'role'    => $_key,
                    'role_id' => constant( $roleClass . '::' . $ucRole ),
                    'data'    => $_value,
                ];
            }
        }
        $acl         = array_values( $acl );
        $model->$key = $acl;
    }
}
