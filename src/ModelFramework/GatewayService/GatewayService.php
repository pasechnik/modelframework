<?php
/**
 * Class GatewayService
 * @package ModelFramework\GatewayService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\GatewayService;

use ModelFramework\ModelService\ModelConfig\ParsedModelConfig;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;

class GatewayService extends GatewayServiceRaw
    implements ModelServiceAwareInterface
{

    use ModelServiceAwareTrait;

    /**
     * @param string             $name
     * @param DataModelInterface $model
     * @param ParsedModelConfig  $modelConfig
     *
     * @return null|MongoGateway
     * @throws \Exception
     */
    public function getGateway(
        $name,
        DataModelInterface $model = null,
        ParsedModelConfig $modelConfig = null
    ) {
        if ($model == null) {
            $model = $this->getModel( $name );
        }
        if ($modelConfig == null) {
            $modelConfig =
                $this->getModelServiceVerify()->getParsedModelConfig( $name );
        }
        $gw = parent::getGateway( '', $model );
        $gw->setParsedModelConfig( $modelConfig );

        return $gw;
    }

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    public function getModel( $modelName )
    {
        return $this->getModelServiceVerify()->get( $modelName );
    }
}
