<?php
/**
 * Class GatewayService
 * @package ModelFramework\GatewayService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\GatewayService;

use ModelFramework\DataModel\DataModelInterface;

interface GatewayServiceInterface
{
    /**
     * @param string             $name
     * @param DataModelInterface $model
     *
     * @return null|MongoGateway
     */
    public function get($name, DataModelInterface $model = null);

    /**
     * @param string             $name
     * @param DataModelInterface $model
     *
     * @return null|MongoGateway
     * @throws \Exception
     */
    public function getGateway($name, DataModelInterface $model = null);
}
