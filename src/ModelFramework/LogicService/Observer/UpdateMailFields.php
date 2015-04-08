<?php
/**
 * Class AbstractObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\LogicService\Logic;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class UpdateMailFields
    implements \SplObserver, SubjectAwareInterface, ConfigAwareInterface
{

    use SubjectAwareTrait;
    use ConfigAwareTrait;


    /**
     * @param \SplSubject|Logic $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );
        $destinationField = $this->getRootConfig()[ 'destination' ];
        $models           = $subject->getEventObject();
        if (!is_array( $models )) {
            $models = [ $models ];
        }


        $linkGW = $subject->getGatewayServiceVerify()->get( 'EmailToMail' );
        foreach ($models as $model) {
            $destinationValue = [];
            $links = $linkGW->find( [ 'mail_id' => (string) $model->_id ] );
            foreach ($links as $link) {
                if(!empty($link->email_id)) {
                    $destinationValue[ $link->email_data ][ $link->email_model_id ] =
                        $link->email_model_title . ' <' . $link->mail_email . '>';
                }else
                {
                    $destinationValue['Other'][] = $link->mail_email;
                }
            }
            $model->$destinationField = $destinationValue;
        }

    }
}