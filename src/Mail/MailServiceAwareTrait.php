<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 15.07.14
 * Time: 19:00
 */

namespace Mail;

trait MailServiceAwareTrait
{
    /**
     * @var MailServiceInterface
     */
    private $_mailService = null;

    /**
     * @return MailServiceInterface
     */
    public function getMailService()
    {
        return $this->_mailService;
    }

    /**
     * @return MailServiceInterface
     *
     * @throws \Exception
     */
    public function getMailServiceVerify()
    {
        $_mailService = $this->getMailService();
        if ($_mailService == null || !$_mailService instanceof MailServiceInterface) {
            throw new \Exception('MailService does not set in the MailServiceAware instance of '.get_class($this));
        }

        return $_mailService;
    }

    /**
     * @param MailServiceInterface $authService
     *
     * @return $this
     */
    public function setMailService(MailServiceInterface $mailService)
    {
        $this->_mailService = $mailService;
    }
}
