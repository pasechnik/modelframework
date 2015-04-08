<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/24/14
 * Time: 5:51 PM
 */

namespace ModelFramework\AclService;

use ModelFramework\DataModel\DataModelInterface;

interface AclServiceInterface
{
    /**
     * @param string $modelName
     *
     * @return AclConfig
     * @throws \Exception
     */
    public function getAclConfig($modelName);

    /**
     * @param string $modelName
     *
     * @return array
     * @throws \Exception
     */
    public function getVisibleFields($modelName);


    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function get($modelName);

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclDataModel($modelName);
}
