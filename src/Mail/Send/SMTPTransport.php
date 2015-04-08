<?php

namespace Mail\Send;

use Wepo\Model\Mail;
//////
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

/**
 * Description of SMPTMailSend
 *
 * Sending emails via SMTP protocol
 *
 * @author KSV
 */
class SMTPTransport extends BaseTransport
{
    /**
     * @var SmtpTransport
     */
    protected $transport = null;
    protected $setting   = null;

    protected function openTransport()
    {
        try {
            $this->transport = new Smtp();
            $this->transport->setOptions(new SmtpOptions($this->setting['protocol_settings']));
        } catch (\Exception $ex) {
            throw new \Exception('Wrong mail server send connection. Check connection settings or asc administrator');
//            throw $ex;
        }
    }

    protected function closeTransport()
    {
        $this->transport->disconnect();
    }

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
        return parent::sendMail($mail);
    }
}
