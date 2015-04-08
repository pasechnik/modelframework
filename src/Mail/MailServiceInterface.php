<?php
namespace Mail;

use Zend\ServiceManager\ServiceLocatorInterface;

interface MailServiceInterface
{
    /**
     * purpose you want to use service: receive or send emails
     * @param MailService::PURPOSE_SEND|MailService::PURPOSE_RECEIVE $purpose
     *
     * name of protocol you want to use
     * @param string $protocolName
     *
     * settings specified for protocol (look zf2 help)
     * @param array $setting
     *
     * id of setting, witch used to get ot send mail. Can be used to prevent fetching same mails more
     * @param null $settingId
     *
     * @return Receive\BaseTransport|Send\BaseTransport
     * @throws \Exception
     */
    public function getGateway($purpose, $protocolName, Array $setting, $settingId = null);

    public function getServiceLocator();

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator);
}
