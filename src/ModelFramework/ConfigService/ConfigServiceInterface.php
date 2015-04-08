<?php
/**
 * Class ConfigServiceInterface
 * @package ModelFramework\ConfigService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ConfigService;

use ModelFramework\DataModel\DataModelInterface;

interface ConfigServiceInterface
{
    /**
     * @param string             $domain
     * @param string             $keyName
     * @param DataModelInterface $configObject
     *
     * @return DataModelInterface|ConfigData|DataModelInterface|null
     * @throws \Exception
     */
    public function getConfig($domain, $keyName, DataModelInterface $configObject);

    /**
     * @param string             $domain
     * @param string             $keyName
     * @param DataModelInterface $configObject
     *
     * @return DataModelInterface|ConfigData|DataModelInterface|null
     * @throws \Exception
     */
    public function get($domain, $keyName, DataModelInterface $configObject);

    /**
     * @param string             $keyName
     * @param DataModelInterface $configObject
     *
     * @return DataModelInterface|null
     * @throws \Exception
     */
    public function getByObject($keyName, DataModelInterface $configObject);

    /**
     * @param DataModelInterface $configObject
     *
     * @return array|bool|int|mixed
     * @throws \Exception
     */
    public function saveByObject(DataModelInterface $configObject);
}
