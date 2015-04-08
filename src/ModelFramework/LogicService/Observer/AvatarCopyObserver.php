<?php
/**
 * Class AgeObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class AvatarCopyObserver extends AbstractConfigObserver
{

    public function process( $model, $key, $value )
    {
        if (!empty( $model->$key )) {

            $subject = $this->getSubject();
            $fs      = $subject->getFileServiceVerify();
            $dest    = $fs->setDestenation( $model->$key, true, 'patient' );
            $from    = $fs->checkDestenation( $model->$key, true, 'lead' );
            $fs->moveFile( $from, $dest );
            $this->getSubject()->getGatewayServiceVerify()->get( 'Patient' )
                 ->update( [ $key => basename( $dest ) ], [ $key => basename( $from ) ] );
            $model->$key = basename( $dest );
        }
    }
}
