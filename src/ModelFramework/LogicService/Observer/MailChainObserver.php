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

class MailChainObserver
    implements \SplObserver, ConfigAwareInterface, SubjectAwareInterface
{

    use ConfigAwareTrait, SubjectAwareTrait;

    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );

        $mails  = $subject->getEventObject();
        $action = $this->getRootConfig()[ 'action' ];
        if (!( is_array( $mails ) || $mails instanceof ResultSetInterface )) {
            $mails = [ $mails ];
        }
        switch ($action) {
            case 'update':
                $this->updateMailChains( $mails );
                break;
            case 'delete':
//                $this->unchainMailChains( $mails );
                $unchainedMails = $this->unchainMailChains( $mails );
                $this->updateMailChains( $unchainedMails );
                break;
        }

    }

    public function unchainMailChains( $mails )
    {
        $chainIds = [ ];
        foreach ($mails as $mail) {
            $chainIds[ ] = $mail->chain_id;
        }

        $chainIds = array_unique( $chainIds );
        $this->getSubject()->getGatewayService()->get( 'Mail' )
             ->delete( [ '_id' => $chainIds ] );
        $mailGW         =
            $this->getSubject()->getGatewayService()->get( 'MailDetail' );
        $chainedMails   = $mailGW->find( [ 'chain_id' => $chainIds ] );
        $unchainedMails = [ ];
        foreach ($chainedMails as $mail) {
            $mail->chain_id = '';
            $mailGW->save( $mail );
            if ($mail->status_id != Status::SEND) {
                $unchainedMails[ ] = $mail;
            }
        }
        return $unchainedMails;
    }


    public function updateMailChains( $noChainMails )
    {

        $mailGW       =
            $this->getSubject()->getGatewayService()->get( 'MailDetail' );
        $chainGW      = $this->getSubject()->getGatewayService()->get( 'Mail' );
        $modelService = $this->getSubject()->getModelServiceVerify();

//        $mails        =
//            $mailGW->find( [ 'chain_id' => '', 'owner_id' => $user->id() ] );
//        $noChainMails = [ ];
//        foreach ($mails as $mail) {
//            $noChainMails[ ] = $mail;
//        }

        while (count( $noChainMails )) {
            $mail        = array_pop( $noChainMails );
            $chainMails  = [ ];
            $MInReplyTo  = isset( $mail->header[ 'in-reply-to' ] ) ?
                $mail->header[ 'in-reply-to' ] : null;
            $MMessageId  = $mail->header[ 'message-id' ];
            $MReferences = isset( $mail->header[ 'references' ] ) ?
                $mail->header[ 'references' ] : [ ];
            $chainWhere  = $MReferences;
            if (isset( $MInReplyTo )) {
                array_push( $chainWhere, $MMessageId, $MInReplyTo );
            } else {
                array_push( $chainWhere, $MMessageId );
            }

            foreach ($noChainMails as $key => $mailToCheck) {
                $MInReplyTo     =
                    isset( $mailToCheck->header[ 'in-reply-to' ] ) ?
                        $mailToCheck->header[ 'in-reply-to' ] : null;
                $MMessageId     = $mailToCheck->header[ 'message-id' ];
                $MReferences    =
                    isset( $mailToCheck->header[ 'references' ] ) ?
                        $mailToCheck->header[ 'references' ] : [ ];
                $testChainWhere = $MReferences;
                if (isset( $MInReplyTo )) {
                    array_push( $testChainWhere, $MMessageId, $MInReplyTo );
                } else {
                    array_push( $testChainWhere, $MMessageId );
                }
//                prn($testChainWhere, $chainWhere);
                if (count( array_intersect( $testChainWhere, $chainWhere ) )) {
                    $chainWhere = array_unique( array_merge( $testChainWhere,
                        $chainWhere ) );
                    reset( $noChainMails );
                    unset( $noChainMails[ $key ] );
                    $chainMails[ ] = $mailToCheck;
                }
            }
            $chainMails[ ] = $mail;

//            $chain = $modelService->get( 'Mail' );

            $chain =
                $chainGW->find( [ 'reference' => array_values( $chainWhere ) ] )
                        ->current();
            $chain = isset( $chain ) ? $chain : $modelService->get( 'Mail' );

            $firstMailDate = strtotime( $chain->date );
            $lastMailDate  = strtotime( $chain->date );
            $title         = $chain->title;
            $date          = $chain->date;
            $last_mail     = $chain->last_mail;
            $status        = Status::NEW_;
            foreach ($chainMails as $mail) {
                $mailDate = strtotime( $mail->date );
                $from = $mail->header['from'];
                $to = $mail->header['to'];
//                prn( $mail->date, $mailDate, $firstMailDate, $lastMailDate );
                if (( $mailDate < $firstMailDate ) || !$firstMailDate) {
                    $title         = $mail->title;
                    $firstMailDate = $mailDate;
                }
                if (( $mailDate > $lastMailDate ) || !$lastMailDate) {
                    $date         = $mail->date;//$oldChainDate;
                    $lastMailDate = $mailDate;
                    $last_mail    = $mail->_id;
                    $status       = $mail->status_id;
                }
            }

            $chain->reference =
                array_unique( array_merge( $chainWhere , $chain->reference ) );
            $from=is_array($from)?$from:[$from];
            $to=is_array($to)?$to:[$to];
            $chain->correspondents =
                array_unique( array_merge( $from ,$to, $chain->correspondents ) );
            $chain->title     = $title;
            $chain->date      = $date;
            $chain->last_mail = $last_mail;


            $chain->count     = $chain->count + count( $chainMails );
            $chain->status_id = $status;
//            prn( 'result', $chain );

            try {
                $chain->owner_id = $chainMails[ 0 ]->owner_id ?:
                    $this->getSubject()->getModelAclService()->getUser()->id();

                $this->getSubject()->getLogicService()
                     ->get( 'create', 'Mail' )->trigger( $chain );
//                prn($chain);
//                prn($chainMails);
//                exit;
                $chainGW->save( $chain );
                foreach ($chainMails as $mail) {
                    $mail->chain_id =
                        $chainGW->getLastInsertId() ?: $chain->_id;
//                    prn($mail);
                    $mailGW->save( $mail );
//                    $mail->_id = $mailGW->getLastInsertId() ?: $mail->_id;
                }
            } catch ( \Exception $ex ) {
                throw $ex;
                continue;
            }
        }
    }
}
