<?php
/**
 * Class LogicService
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

use Mail\MailServiceAwareTrait;
use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\FileService\FileServiceAwareInterface;
use ModelFramework\FileService\FileServiceAwareTrait;
use ModelFramework\FilesystemService\FilesystemServiceAwareInterface;
use ModelFramework\FilesystemService\FilesystemServiceAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\LogicService\LogicConfig\LogicConfig;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;
use Zend\Db\ResultSet\ResultSetInterface;

class LogicService
    implements LogicServiceInterface, ConfigServiceAwareInterface,
               GatewayServiceAwareInterface, ParamsAwareInterface,
               AuthServiceAwareInterface, ModelServiceAwareInterface,
               QueryServiceAwareInterface, FileServiceAwareInterface,
	       FilesystemServiceAwareInterface
{

    use ConfigServiceAwareTrait, GatewayServiceAwareTrait, AuthServiceAwareTrait,
        ModelServiceAwareTrait, ParamsAwareTrait, MailServiceAwareTrait,
        QueryServiceAwareTrait, FileServiceAwareTrait, FilesystemServiceAwareTrait;

    public function dispatch( $event )
    {
        $model = $event->getParams();
        if (is_array( $model )) {
            $model = array_shift( $model );
        }
        if ($model instanceof DataModelInterface) {
            $modelName = $model->getModelName();
        } else {
            throw new \Exception( 'Event Params must be instance of DataModel' );
        }

//        $dataLogic = $this->get( $modelName, $modelName ) -> trigger( $model );
        return $this->get( $event->getName(), $modelName )->trigger( $event );

//        return call_user_func( [ $dataLogic, $event->getName() ], $event );
    }

    /**
     * @param string $eventName
     * @param string $modelName
     *
     * @return DataLogic|void
     * @throws \Exception
     */
    public function get( $eventName, $modelName )
    {
        return $this->createLogic( $eventName, $modelName );
    }

    /**
     * @param string $eventName
     * @param string $modelName
     *
     * @return DataLogic|void
     * @throws \Exception
     */
    public function createLogic( $eventName, $modelName )
    {
        $logicConfig =
            $this->getConfigServiceVerify()->getByObject( $modelName . '.' .
                                                          $eventName,
                new LogicConfig() );

        $logic = new Logic();
        if ($logicConfig == null) {
            return $logic;
        }

        $logic->setLogicConfig( $logicConfig );

        $logic->setConfigService( $this->getConfigServiceVerify() );
        $logic->setGatewayService( $this->getGatewayServiceVerify() );
        $logic->setAuthService( $this->getAuthServiceVerify() );
        $logic->setModelService( $this->getModelService() );
        $logic->setLogicService( $this );
        $logic->setMailService( $this->getMailService() );
        $logic->setQueryService( $this->getQueryServiceVerify() );
        $logic->setFileService( $this->getFileServiceVerify() );
        $logic->setFilesystemService( $this->getFilesystemServiceVerify() );
        if ($this->getParams() != null) {
            $logic->setParams( $this->getParams() );
        }
        $logic->init();

        return $logic;
    }

}
