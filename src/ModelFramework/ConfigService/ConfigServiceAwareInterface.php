<?php
/**
 * Class ConfigServiceAwareInterface
 * @package ModelFramework\ConfigService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ConfigService;

interface ConfigServiceAwareInterface
{
    /**
     * @param ConfigServiceInterface $configService
     *
     * @return $this
     */
    public function setConfigService(ConfigServiceInterface $configService);

    /**
     * @return ConfigServiceInterface
     */
    public function getConfigService();

    /**
     * @return ConfigServiceInterface
     * @throws \Exception
     */
    public function getConfigServiceVerify();
}
