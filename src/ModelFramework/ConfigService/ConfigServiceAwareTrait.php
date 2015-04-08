<?php
/**
 * Class ConfigServiceAwareTrait
 * @package ModelFramework\ConfigService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ConfigService;

trait ConfigServiceAwareTrait
{
    private $_configService = null;

    /**
     * @param ConfigServiceInterface $configService
     *
     * @return $this
     */
    public function setConfigService(ConfigServiceInterface $configService)
    {
        $this->_configService = $configService;
        return $this;
    }

    /**
     * @return ConfigServiceInterface
     */
    public function getConfigService()
    {
        return $this->_configService;
    }

    /**
     * @return ConfigServiceInterface
     * @throws \Exception
     */
    public function getConfigServiceVerify()
    {
        $_configService = $this->getConfigService();
        if ($_configService == null || !$_configService instanceof ConfigServiceInterface) {
            throw new \Exception('ConfigService does not set in the ConfigServiceAware instance of '.
                                  get_class($this));
        }

        return $_configService;
    }
}
