<?php

namespace ModelFramework\ConfigService;

use ModelFramework\Utility\Arr;

trait ConfigAwareTrait
{
    /**
     * @var array
     */
    private $_rootConfig = null;

    /**
     * @param array $config
     *
     * @return $this
     * @throws \Exception
     */
    public function setRootConfig($config)
    {
        if (!is_array($config)) {
            throw new \Exception('Config must be an array');
        }
        $this->_rootConfig = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getRootConfig()
    {
        return $this->_rootConfig;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getRootConfigVerify()
    {
        $_rootConfig = $this->getRootConfig();
        if ($_rootConfig == null || !is_array($_rootConfig)) {
            throw new \Exception('System config array does not set in the ConfigAware instance of '.
                                  get_class($this));
        }

        return $_rootConfig;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getConfigPart($type)
    {
        return Arr::getDoubtField($this->getRootConfigVerify(), $type, [ ]);
    }

    /**
     * @param string $domain
     * @param string $key
     * @param null   $subKey
     * @param array  $default
     *
     *
     * @return null
     */
    public function getConfigDomainPart($domain, $key, $subKey = null, $default = [])
    {
        $domainConfig = Arr::getDoubtField($this->getConfigPart($domain), $key, $default);

        if ($subKey !== null) {
            $subConfig = Arr::getDoubtField($domainConfig, $subKey, $default);

//            if ( $subConfig === $default && strtolower($subKey)!==$subKey )
//            {
//                $keyMap = [];
//                foreach (  array_keys($domainConfig) as $_key )
//                {
//                    $keyMap[ strtolower($_key) ] = $_key;
//                }
//                $subConfig = Arr::getDoubtField( $domainConfig, $keyMap[ strtolower($subKey) ], $default );
//            }

            $domainConfig = $subConfig;
        }

        return $domainConfig;
    }

    /**
     * @param string $domain
     * @param string $key
     * @param array  $default
     *
     * @return array
     */
    public function getConfigDomainSystem($domain, $key = null, $default = [])
    {
        return $this->getConfigDomainPart($domain, 'system', $key, $default);
    }

    /**
     * @param string $domain
     * @param string $key
     * @param array  $default
     *
     * @return array
     */
    public function getConfigDomainCustom($domain, $key = null, $default = [])
    {
        return $this->getConfigDomainPart($domain, 'custom', $key, $default);
    }
}
