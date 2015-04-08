<?php
/**
 * Class ConfigService
 *
 * @package ModelFramework\ConfigService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ConfigService;

use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\Utility\Arr;

class ConfigService
    implements ConfigServiceInterface, GatewayServiceAwareInterface,
               ConfigAwareInterface
{

    use GatewayServiceAwareTrait, ConfigAwareTrait;

    /**
     * @param string             $domain
     * @param string             $keyName
     * @param DataModelInterface $configObject
     *
     * @return DataModelInterface|null
     * @throws \Exception
     */
    protected function getConfigFromDb(
        $domain,
        $keyName,
        DataModelInterface $configObject
    ) {
        //        $configData = new ConfigData();
        $configData = $this->getGatewayServiceVerify()
                           ->get( $configObject->getModelName(), $configObject )
                           ->findOne( [ 'key' => strtolower( $keyName ) ] );
        if ($configData == null) {
            //            $configArray = Arr::getDoubtField( $this->getConfigDomainCustom( $domain ), $keyName, null );
            $configArray
                = $this->getConfigDomainCustom( $domain, $keyName, null );
            if ($configArray == null) {
                return;
//                throw new \Exception( ' unknown config for model ' . $keyName );
            }
            $configData = clone $configObject;
            $configData->exchangeArray( $configArray );
//            $configData->key = $keyName;
//            prn($this->getGatewayServiceVerify()->get( $configData -> getModelName(), $configData ));
//            $this->getGatewayServiceVerify()->get( $configData -> getModelName(), $configData )->save( $configData );
        }

        return $configData;
    }

    /**
     * @param DataModelInterface $configObject
     *
     * @return array|bool|int|mixed
     * @throws \Exception
     */
    public function saveByObject( DataModelInterface $configObject )
    {
        if (empty( $configObject->key )) {
            throw new \Exception( '"key" field must be set in ' .
                                  $configObject->getModelName() .
                                  ' configuration' );
        }
        $configObject->key = strtolower( $configObject->key );

        return $this->getGatewayServiceVerify()
                    ->get( $configObject->getModelName(), $configObject )
                    ->save( $configObject );
    }

    /**
     * @param string             $domain
     * @param string             $keyName
     * @param DataModelInterface $configObject
     *
     * @return DataModelInterface|ConfigData|DataModelInterface|null
     * @throws \Exception
     */
    public function getConfig(
        $domain,
        $keyName,
        DataModelInterface $configObject
    ) {
        $configArray
            = Arr::getDoubtField( $this->getConfigDomainSystem( $domain ),
            $keyName, null );
        if ($configArray == null) {
            $configObject
                = $this->getConfigFromDb( $domain, $keyName, $configObject );
        } else {
            $configObject->exchangeArray( $configArray );
        }

//        if ( $configObject == null )
//        {
//            return null;
////            throw new \Exception( 'Can\'t find configuration for the ' . $keyName . 'model' );
//        }

        return $configObject;
    }

    /**
     * @param string             $domain
     * @param string             $keyName
     * @param DataModelInterface $configObject
     *
     * @return DataModelInterface|DataModelInterface|null
     */
    public function get( $domain, $keyName, DataModelInterface $configObject )
    {
        return $this->getConfig( $domain, $keyName, $configObject );
    }

    /**
     * @param string $keyName
     *
     * @return Config
     * @throws \Exception
     */
    public function getByObject( $keyName, DataModelInterface $configObject )
    {
//        if( $configObject->getModelName() == 'ModelConfig'&& $keyName == 'User')
//        {
//            $configObject = $this->getConfig( $configObject->getModelName(), $keyName,
//                $configObject);
//            $configObject->key = 'User';
//            $this->saveByObject($configObject);
//            prn($keyName, $configObject->getModelName());
////            exit;
//        }
        return $this->getConfig( $configObject->getModelName(), $keyName,
            $configObject );
    }

    public function fetchAllByObject( DataModelInterface $configObject )
    {
        $configArray =
            Arr::getDoubtField( $this->getConfigPart( $configObject->getModelName() ),
                'custom', [ ] );
        $result      = [ ];
        foreach ($configArray as $config) {
            $result[ ] = clone $configObject->exchangeArray( $config );
        }

        return $result;
    }

    public function saveConfigToDbByObject( DataModelInterface $configObject ){
        $configArray =
            Arr::getDoubtField( $this->getConfigPart( $configObject->getModelName() ),
                'custom', [ ] );
        foreach ($configArray as $config) {
            $object = clone $configObject;
            $object->exchangeArray( $config );
            $this->getGatewayServiceVerify()
                 ->get( $configObject->getModelName(), $configObject )
                 ->save( $object );
        }
        return $this;
    }
}
