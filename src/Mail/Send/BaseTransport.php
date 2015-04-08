<?php

namespace Mail\Send;

use Mail\Compose\MailConvert;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use Zend\Mail\Storage\Message;

/**
 * Description of BaseMailSend
 *
 * @author KSV
 */
abstract class BaseTransport implements GatewayServiceAwareInterface,
                                        ModelServiceAwareInterface
{

    use GatewayServiceAwareTrait, ModelServiceAwareTrait;

    /**
     * @var Object
     */
    protected $transport = null;

    /**
     * @var Array
     */
    protected $setting   = null;

    /**
     * @var MailConvert
     */
    protected $convertor = null;

    /**
     * initialize sending service by mail convertor and array of setting
     * for mail transport
     *
     * @param array       $setting
     * @param MailConvert $convertor
     *
     * @throws \Exception
     */
    public function __construct(Array $setting, MailConvert $convertor)
    {
        $this->setting = $setting;
        $this->convertor = $convertor;
    }

    /**
     * open transport to send mail
     *
     * @return mixed
     */
    abstract protected function openTransport();

    /**
     * close transport
     *
     * @return mixed
     */
    abstract protected function closeTransport();

    /**
     * send mail
     *
     * array or object of type you set while initialize service
     * @param Object|array $mail
     *
     * @throws \Exception $ex
     *
     * @return Mail
     */
    public function sendMail($mail)
    {
        if (!is_array($mail)) {
            try {
                $arrayMail = $mail->toArray();
            } catch (\Exception $ex) {
                throw new \Exception('impossible to convert '.get_class($mail).' to mail array');
            }
        } else {
            $arrayMail = $mail;
        }

        $this->openTransport();

        $header = $arrayMail['header'];

        $sendingMail = $this->convertor->convertToSendFormat($arrayMail);

//        prn($sendingMail->toString());

        try {
            $this->transport->send($sendingMail);
        } catch (\Exception $ex) {
            //create checking exception to output normal view, that describes problem to user
            throw new \Exception('Mail format exception. Asc administrator to fix the problem');
//            throw $ex;
        }

        $headers=$sendingMail->getHeaders()->toArray();
        if (isset($headers['Message-ID'])){
            $header['message-id'] = $headers['Message-ID'];
        }

        if (is_array($mail)) {
            $mail['header'] = $header;
        } else {
            $mail->header = $header;
        }
        $this->closeTransport();

        return $mail;
    }
}
