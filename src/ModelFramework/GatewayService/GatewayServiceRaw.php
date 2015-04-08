<?php
/**
 * Class GatewayService
 * @package ModelFramework\GatewayService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\GatewayService;

use ModelFramework\BaseService\ServiceLocatorAwareTrait;
use Zend\Db\ResultSet\ResultSet;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\Utility\Arr;
use ModelFramework\Utility\Obj;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class GatewayServiceRaw
    implements GatewayServiceInterface, ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    /**
     * @param string             $name
     * @param DataModelInterface $model
     *
     * @return null|MongoGateway
     */
    public function get( $name, DataModelInterface $model = null )
    {
        return $this->getGateway( $name, $model );
    }

    /**
     * @param string             $name
     * @param DataModelInterface $model
     *
     * @return null|MongoGateway
     * @throws \Exception
     */
    public function getGateway( $name, DataModelInterface $model = null )
    {
        if ($model == null) {
            throw new \Exception( ' Raw Gateway needs a DataModelInterface instance as the second parameter ' );
        }
        $adapterConfig = $this->getConfig( $model );
        $gwName        = Arr::getDoubtField( $adapterConfig, 'gateway', null );
        if ($gwName === null) {
            $gwName = Arr::getDoubtField( $adapterConfig, 'driver', null );
        }
        if ($gwName === null) {
            throw new \Exception( 'Unknown gateway' );
        }
        // create resultSet prototype
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype( $model );
        // create custom transport for the model
        $dbAdapter = $this->getServiceLocator()->get( $model->_adapter );
        // use general gw class
        if ($gwName == 'Pdo') {
            throw new \Exception( ' TODO: Needs to implement PDO Gateway create !! ' );
        }
        $gw = Obj::create( '\\ModelFramework\\GatewayService\\MongoGateway',
            [
                'table'              => $model->getTableName(),
                'adapter'            => $dbAdapter,
                'resultSetPrototype' => $resultSetPrototype
            ] );

        return $gw;
    }

    /**
     * @param DataModelInterface $model
     *
     * @return array
     * @throws \Exception
     */
    protected function getConfig( DataModelInterface $model )
    {
        if (!$this->getServiceLocator()->has( $model->_adapter )) {
            throw new \Exception( 'Wrong adapter name ' . $model->_adapter .
                                  ' in model ' . $model->getModelName() );
        }

        return $this->getServiceLocator()->get( $model->_adapter )->getDriver()
                    ->getConnection()
                    ->getConnectionParameters();
    }
}
