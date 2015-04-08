<?php
/**
 * Class DataSchemaService
 * @package ModelFramework\DataSchemaService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataSchemaService;

use ModelFramework\DataModel\Custom\ViewConfigData;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;

class DataSchemaService implements DataSchemaServiceInterface, GatewayServiceAwareInterface
{
    use GatewayServiceAwareTrait;

    /**
     * @var array
     */
    protected $_dataSchemas = [ ];

    /**
     * @var array
     */
    protected $_dbConfig = [ ];

    protected function getKeyName($modelName, $viewName)
    {
        return $modelName.'.'.$viewName;
    }

    protected function getConfigFromDb($modelName)
    {
        $dataSchema = $this->getGatewayServiceVerify()->getGateway('ModelView', new DataSchema())->findOne(
            [ 'model' => $modelName ]
        );
        if ($dataSchema == null) {
            $configArray = $this->_dbConfig[ $modelName ];
            if ($configArray == null) {
                throw new \Exception(' unknown config for model '.$modelName);
            }
            $dataSchema = new DataSchema($configArray);
//            $configData->exchangeArray( $configArray );
//            $this->getGatewayServiceVerify()->get( 'ConfigData', $viewConfigData )->save( $viewConfigData );
        }

        return $dataSchema;
    }

    /**
     * @param $modelName
     *
     * @return DataSchema
     * @throws \Exception
     */
    public function getDataSchema($modelName)
    {
        $dataSchema = $this->getConfigFromDb($modelName);
        if ($dataSchema == null) {
            $dataSchemaArray = $this->_dataSchemas[ $modelName ];
            if ($dataSchemaArray !== null) {
                $dataSchema = new DataSchema($dataSchemaArray);
            } else {
                throw new \Exception('Unknown data schema for '.$modelName);
            }
        }

        return $dataSchema;
    }

    public function get($modelName)
    {
        return $this->getDataSchema($modelName);
    }
}
