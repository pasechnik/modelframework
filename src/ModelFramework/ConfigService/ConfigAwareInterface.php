<?php

namespace ModelFramework\ConfigService;

interface ConfigAwareInterface
{
    /**
     * @param array $config
     *
     * @return $this
     * @throws \Exception
     */
    public function setRootConfig($config);

    /**
     * @return array
     */
    public function getRootConfig();

    /**
     * @return array
     * @throws \Exception
     */
    public function getRootConfigVerify();

    /**
     * @param  string     $type
     * @throws \Exception
     *
     * @return array
     */
    public function getConfigPart($type);

    /**
     * @param string $domain
     * @param string $key
     * @param null   $subKey
     * @param array  $default
     *
     *
     * @return null
     */
    public function getConfigDomainPart($domain, $key, $subKey = null, $default = []);

    /**
     * @param string $domain
     * @param string $key
     * @param array  $default
     *
     * @return array
     */
    public function getConfigDomainSystem($domain, $key = null, $default = []);

    /**
     * @param string $domain
     * @param string $key
     * @param array  $default
     *
     * @return array
     */
    public function getConfigDomainCustom($domain, $key = null, $default = []);
}
