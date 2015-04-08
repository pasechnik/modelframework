<?php
/**
 * Class Logic
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

use Mail\MailServiceAwareTrait;
use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\BaseService\AbstractService;
use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\FileService\FileServiceAwareInterface;
use ModelFramework\FileService\FileServiceAwareTrait;
use ModelFramework\FilesystemService\FilesystemServiceAwareInterface;
use ModelFramework\FilesystemService\FilesystemServiceAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\LogicService\LogicConfig\LogicConfigAwareInterface;
use ModelFramework\LogicService\LogicConfig\LogicConfigAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use ModelFramework\Utility\Arr;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;

class Logic extends AbstractService
    implements GatewayServiceAwareInterface, LogicConfigAwareInterface,
               AuthServiceAwareInterface, ParamsAwareInterface, \SplSubject,
               LogicServiceAwareInterface, ConfigServiceAwareInterface,
               QueryServiceAwareInterface, FileServiceAwareInterface, FilesystemServiceAwareInterface
{
    use ModelServiceAwareTrait, GatewayServiceAwareTrait, LogicConfigAwareTrait,
        AuthServiceAwareTrait, ParamsAwareTrait, LogicServiceAwareTrait,
        ConfigServiceAwareTrait, MailServiceAwareTrait, QueryServiceAwareTrait,
        FileServiceAwareTrait, FilesystemServiceAwareTrait;

    /**
     * @var array|DataModel|null
     */
    private $_eventObject = null;

    protected $allowed_observers = [
        'ConcatenationObserver',
        'FillJoinsObserver',
        'ConstantObserver',
        'NewItemObserver',
        'ChangerObserver',
        'ParamsObserver',
        'CleanObserver',
        'DefaultObserver',
        'SaveObserver',
        'OwnerObserver',
        'DateObserver',
        'AgeObserver',
        'MainUserObserver',
        'FormatObserver',
        'CopyObserver',
        'ConditionObserver',
        'RecycleObserver',
        'AclObserver',
        'PriceObserver',
        'FormulaObserver',
        'DetailsSumObserver',
        'TriggerObserver',
        'ConvertObserver',
        'DebugObserver',
        'SetAsDefaultObserver',
        'MailSyncObserver',
        'MailChainObserver',
        'MailLinkObserver',
        'EmailObserver',
        'UpdateMailFields',
        'CheckNumFieldObserver',
        'AvatarCopyObserver',
        'IsDefaultObserver',
        'TriggerByModelObserver',
        'UniqidObserver',
        'RecycleObserver',
        'ParseLinkObserver'
    ];

    protected $observers = [ ];
    private $_data = [ ];

    public function getData()
    {
        return $this->_data;
    }

    public function setData(array $data)
    {
        $this->_data = Arr::merge($this->_data, $data);
//        $this->_data = $data;
    }

    protected function clearData()
    {
        $this->_data = [ ];
    }

    public function attach(\SplObserver $observer)
    {
        $this->observers[ ] = $observer;
    }

    public function detach(\SplObserver $observer)
    {
        $key = array_search($observer, $this->observers);
        if ($key) {
            unset($this->observers[ $key ]);
        }
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function init()
    {
        foreach ($this->getLogicConfigVerify()->observers as $observer => $obConfig) {
            if (is_numeric($observer)) {
                $observer = $obConfig;
                $obConfig = null;
            }
            if (!in_array($observer, $this->allowed_observers)) {
                throw new \Exception($observer.' is not allowed in '.get_class($this));
            }
            $observerClassName = 'ModelFramework\LogicService\Observer\\'.$observer;
            $_obs              = new $observerClassName();
            if (!empty($obConfig) && $_obs instanceof ConfigAwareInterface) {
                $_obs->setRootConfig($obConfig);
            }
            $this->attach($_obs);
        }
    }

    protected function getRules()
    {
        return $this->getLogicConfigVerify()->rules;
    }

    public function getModelName()
    {
        return $this->getLogicConfigVerify()->model;
    }

    /**
     * @param array|DataModelInterface|null $eventObject
     *
     * @return $this
     */
    public function setEventObject($eventObject)
    {
        $this->_eventObject = $eventObject;

        return $this;
    }

    /**
     * @return array|DataModel|null
     */
    public function getEventObject()
    {
        return $this->_eventObject;
    }

    public function process()
    {
        $this->notify();
    }

    /**
     * @param array|DataModelInterface $eventObject
     *
     * @throws \Exception
     */
    public function trigger($eventObject)
    {
        //        $model = $eventObject;
//
//        if ( is_array( $eventObject ) )
//        {
//            $model = reset( $eventObject );
//        }
//        else
//        {
//            if ( $eventObject instanceof ResultSetInterface )
//            {
//                $model = $eventObject->getArrayObjectPrototype();
//            }
//
//            if ( !$model instanceof DataModelInterface )
//            {
//                throw new \Exception( 'Event Param must implement DataModelInterface ' );
//            }
//
//        }
        $this->setEventObject($eventObject);

        $this->process();
    }
}
