<?php
/**
 * Class AclObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;
use Wepo\Model\Status;

class MailLinkObserver
    implements \SplObserver, ConfigAwareInterface, SubjectAwareInterface
{

    use ConfigAwareTrait, SubjectAwareTrait;

    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );

        $mails  = $subject->getEventObject();
        $action = $this->getRootConfig()[ 'action' ];
        switch ($action) {
            case 'update':
                if (!( is_array( $mails ) ||
                       $mails instanceof ResultSetInterface )
                ) {
                    $mails = [ $mails ];
                }

                foreach ($mails as $mail) {
                    $this->createEmailToMail( $mail );
                }
                break;
            case 'delete':
                $this->deleteEmailToMail( $mails );
                break;
        }
    }

    public function createEmailToMail( $mail )
    {
        $searchValues = $mail->type == 'inbox' ? $mail->header[ 'from' ] :
            $mail->header[ 'to' ];
        if (!is_array($searchValues)){
            $searchValues=[$searchValues];
        }
        $emailGW      =
            $this->getSubject()->getGatewayServiceVerify()->get( 'Email' );
        $linkGW       =
            $this->getSubject()->getGatewayServiceVerify()
                 ->get( 'EmailToMail' );

        $nonLinkedEmails = $emailGW->find( [ 'email' => $searchValues ] );
        foreach ($nonLinkedEmails as $email) {
            $link             = $this->getSubject()->getModelServiceVerify()
                                     ->get( 'EmailToMail' );
            $link->email_id   = $email->_id;
            $link->mail_id    = (string) $mail->_id;
            $link->mail_field = 'from_to_title';
            $link->mail_title = $mail->title;
            $link->mail_email = $email->email;
            $link->_acl       = $mail->_acl;
            $link->owner_id   = $mail->owner_id;
            $this->getSubject()->getLogicService()
                 ->get( 'update', 'EmailToMail' )->trigger( $link );
            unset( $searchValues[ array_search( $link->mail_email,
                    $searchValues ) ] );
            $linkGW->save( $link );
        }
        if (count( $searchValues )) {
            foreach ($searchValues as $email) {
                $newLink             =
                    $this->getSubject()->getModelServiceVerify()
                         ->get( 'EmailToMail' );
                $newLink->mail_email = $email;
                $newLink->mail_id    = (string) $mail->_id;
                $newLink->mail_field = 'from_to_title';
                $newLink->mail_title = $mail->title;
                $newLink->_acl       = $mail->_acl;
                $newLink->owner_id   = $mail->owner_id;
                $this->getSubject()->getLogicService()
                     ->get( 'update', 'EmailToMail' )->trigger( $newLink );
                $linkGW->save( $newLink );
            }
        }

//        $this->getSubject()->getLogicService()
//             ->get( 'updateTitle', 'MailDetail' )->trigger( $mail );
    }

    public function deleteEmailToMail( $mails )
    {
        $ids = [ ];
        foreach ($mails as $mail) {
            $ids[ ] = $mail->id();
        }

        $emtmGW = $this->getSubject()->getGatewayServiceVerify()
                       ->get( 'EmailToMail' );
        $emtmGW->delete( [ 'mail_id' => $ids ] );
    }
}
