<?php
namespace ModelFramework\AuthService;

interface AuthServiceAwareInterface
{
    /**
     * @return AuthServiceInterface
     */
    public function getAuthService();

    /**
     * @return AuthServiceInterface
     * @throws \Exception
     */
    public function getAuthServiceVerify();

    /**
     * @param AuthServiceInterface $authService
     *
     * @return $this
     */
    public function setAuthService(AuthServiceInterface $authService);
}
