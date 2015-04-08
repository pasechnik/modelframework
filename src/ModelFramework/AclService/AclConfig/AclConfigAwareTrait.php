<?php
/**
 * Class AclConfigAwareTrait
 * @package namespace ModelFramework\AclService\AclConfig
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\AclService\AclConfig;

use ModelFramework\DataModel\DataModelInterface;

trait AclConfigAwareTrait
{

    /**
     * @var AclConfig|DataModelInterface
     */
    private $_aclConfig = null;

    /**
     * @param AclConfig|DataModelInterface $aclConfig
     *
     * @return $this
     */
    public function setAclConfig( AclConfig $aclConfig )
    {
        $this->_aclConfig = $aclConfig;

        return $this;
    }

    /**
     * @return AclConfig|DataModelInterface
     *
     */
    public function getAclConfig()
    {
        return $this->_aclConfig;
    }

    /**
     * @return AclConfig|DataModelInterface
     * @throws \Exception
     */
    public function getAclConfigVerify()
    {
        $aclConfig = $this->getAclConfig();
        if ($aclConfig == null || !$aclConfig instanceof AclConfig) {
            throw new \Exception( 'AclConfig is not set' );
        }

        return $aclConfig;
    }
}
