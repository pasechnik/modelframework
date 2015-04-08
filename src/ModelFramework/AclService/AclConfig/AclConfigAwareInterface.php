<?php
/**
 * Class AclConfigAwareInterface
 * @package namespace ModelFramework\AclService\AclConfig
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\AclService\AclConfig;

interface AclConfigAwareInterface
{
    /**
     * @param AclConfig $aclConfig
     *
     * @return $this
     */
    public function setAclConfig(AclConfig $aclConfig);

    /**
     * @return AclConfig
     */
    public function getAclConfig();

    /**
     * @return AclConfig
     * @throws \Exception
     */
    public function getAclConfigVerify();
}
