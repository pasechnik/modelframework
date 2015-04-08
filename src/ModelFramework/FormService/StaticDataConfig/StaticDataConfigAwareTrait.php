<?php
/**
 * Class LogicConfigAwareTrait
 * @package ModelFramework\DataModel\Custom
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService\StaticDataConfig;

use ModelFramework\DataModel\DataModelInterface;

trait StaticDataConfigAwareTrait
{
    /**
     * @var StaticDataConfig|DataModelInterface
     */
    private $_staticDataConfig = null;

    /**
     * @param StaticDataConfig|DataModelInterface $logicConfig
     *
     * @return $this
     */
    public function setStaticDataConfig(StaticDataConfig $staticDataConfig)
    {
        $this->$_staticDataConfig = $staticDataConfig;
    }

    /**
     * @return StaticDataConfig|DataModelInterface
     *
     */
    public function getStaticDataConfig()
    {
        return $this->$_staticDataConfig;
    }

    /**
     * @return StaticDataConfig|DataModelInterface
     * @throws \Exception
     */
    public function getStaticDataConfigVerify()
    {
        $staticDataConfig = $this->getStaticDataConfig();
        if ($staticDataConfig == null || !$staticDataConfig instanceof StaticDataConfig) {
            throw new \Exception('Static Data Config does not set set in StaticData');
        }

        return $staticDataConfig;
    }
}
