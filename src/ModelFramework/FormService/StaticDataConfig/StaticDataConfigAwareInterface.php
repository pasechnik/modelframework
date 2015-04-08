<?php
/**
 * Class StaticDataConfigAwareInterface
 *
 * @package ModelFramework\DataModel\Custom
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
namespace ModelFramework\FormService\StaticDataConfig;

interface StaticDataConfigAwareInterface
{

    /**
     * @param StaticDataConfig $logicConfig
     *
     * @return $this
     */
    public function setStaticDataConfig(StaticDataConfig $logicConfig);

    /**
     * @return StaticDataConfig
     */
    public function getStaticDataConfig();

    /**
     * @return StaticDataConfig
     * @throws \Exception
     */
    public function getStaticDataConfigVerify();
}
