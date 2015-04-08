<?php

namespace ModelFramework\AclService;

interface AclServiceAwareInterface
{
    /**
     * @param AclServiceInterface $aclService
     *
     * @return $this
     */
    public function setAclService(AclServiceInterface $aclService);

    /**
     * @return AclServiceInterface
     */
    public function getAclService();

    /**
     * @return AclServiceInterface
     * @throws \Exception
     */
    public function getAclServiceVerify();
}
