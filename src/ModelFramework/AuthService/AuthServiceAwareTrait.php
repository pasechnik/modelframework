<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 15.07.14
 * Time: 19:00
 */

namespace ModelFramework\AuthService;

trait AuthServiceAwareTrait
{
    /**
     * @var AuthServiceInterface
     */
    private $_authService = null;

    /**
     * @return AuthServiceInterface
     */
    public function getAuthService()
    {
        return $this->_authService;
    }

    /**
     * @return AuthServiceInterface
     *
     * @throws \Exception
     */
    public function getAuthServiceVerify()
    {
        $_authService = $this->getAuthService();
        if ($_authService == null || !$_authService instanceof AuthServiceInterface) {
            throw new \Exception('AuthService does not set in the AuthServiceAware instance of '.get_class($this));
        }

        return $_authService;
    }

    /**
     * @param AuthServiceInterface $authService
     *
     * @return $this
     */
    public function setAuthService(AuthServiceInterface $authService)
    {
        $this->_authService = $authService;
    }
}
