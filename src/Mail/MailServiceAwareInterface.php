<?php
namespace Mail;

interface MailServiceAwareInterface
{
    /**
     * @return MailServiceInterface
     */
    public function getMailService();

    /**
     * @return MailServiceInterface
     * @throws \Exception
     */
    public function getMailServiceVerify();

    /**
     * @param MailServiceInterface $authService
     *
     * @return $this
     */
    public function setMailService(MailServiceInterface $authService);
}
