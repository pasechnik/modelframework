<?php

namespace Wepo\Controller;

use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\FormService\StaticDataConfig\StaticDataConfig;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\GatewayService\MongoGateway;
use ModelFramework\LogicService\LogicServiceAwareInterface;
use ModelFramework\LogicService\LogicServiceAwareTrait;
use ModelFramework\ModelService\ModelConfig\ModelConfig;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\Utility\Obj;
use Wepo\Model\Status;
use Zend\Console\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;

class ModelController extends AbstractActionController
    implements GatewayServiceAwareInterface, AuthServiceAwareInterface,
               LogicServiceAwareInterface, ModelServiceAwareInterface
{

    use GatewayServiceAwareTrait, AuthServiceAwareTrait, LogicServiceAwareTrait, ModelServiceAwareTrait;
    private $_gatewayServiceRaw;

    public function indexAction()
    {
        $results['title']      = 'Tune & apply export';
        $results['listLength'] = 0;

        $sl = $this->getServiceLocator();// ->get('MonZend\Db\Adapter\Adapter');

        $m        = new \MonZend\Db\Adapter\AdapterAbstractServiceFactory();
        $adapters = $m->getAdapterNames($sl);

        $export = [];

        $main_user = $this->getGatewayServiceVerify()->get('MainUser')
            ->get($this->user()->main_id);
        $dbsName   = $this->getGatewayServiceVerify()->get('MainDb')
                         ->find(['company_id' => $main_user->company_id])
                         ->toArray()[0]['dbname'];
//        prn($dbsName);
//        prn($adapters);

//        where export adapters should be stored? (now it's intended to be stored in conf file)
//        check if some export data_bases belong to current data_base
        foreach ($adapters as $key => $adapter) {
            $utility   = new Obj();
            $dbAdapter = $this->getServiceLocator()->get($adapter);
            if (in_array('export_wepo_settings',
                $dbAdapter->getDriver()->getConnection()->getDB()
                    ->getCollectionNames())) {
                $gw = new MongoGateway('export_wepo_settings', $dbAdapter);
//                prn($gw->find()->toArray(),$dbsName);
                if (count($gw->find([
                    '_owner'   => $dbsName,
                    '_purpose' => 'db_settings'
                ])->current())) {
                    $modelsConvertConfig
                                           = $gw->find(['_purpose' => 'model_settings']);
                    $export[$adapter]      = $modelsConvertConfig->toArray();
                    $results['listLength'] = max($results['listLength'],
                        count($export[$adapter]));
                }
            }
        }

        $results['export_tables'] = $export;

        return $results;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        parent::setServiceLocator($serviceLocator);
        if ( !$serviceLocator instanceof \Zend\Mvc\Controller\ControllerManager
        ) {
            $this
                ->setGatewayService($serviceLocator->get('ModelFramework\GatewayService'));
            $this
                ->_gatewayServiceRaw
                = $serviceLocator->get('ModelFramework\GatewayServiceRaw');
            $this
                ->setAuthService($serviceLocator->get('ModelFramework\AuthService'));
            $this->setLogicService($serviceLocator->get('ModelFramework\LogicService'));
            $this->setModelService($serviceLocator->get('ModelFramework\ModelService'));
        }
    }

    public function user()
    {
        return $this->getAuthServiceVerify()->getUser();
    }

    public function dbcmprAction()
    {
        $results['from']  = $this->params('db', null);
        $results['db']    = $this->params('db', null);
        $results['title'] = 'Looking over "' . $results['from'] . '" database ';

        if ($results['from'] == null) {
            throw new \Exception('Can\'t get out without db param');
        }

        $request = $this->getRequest();

        $dbAdapter = $this->getServiceLocator()->get($results['from']);
        $gw        = new MongoGateway('export_wepo_settings', $dbAdapter);
        $dbSetting = $gw->find(['_purpose' => 'db_settings'])->current();

        if ($request->isPost()) {
            $res         = $gw->find(['_purpose' => 'model_settings'])
                ->toArray();
            $oldSettings = [];

            array_walk($res,
                function (&$item, &$index) use (&$oldSettings) {
                    $oldSettings[$item['_models']['from']]['to']
                        = $item['_models']['to'];
                    $oldSettings[$item['_models']['from']]['_id']
                        = $item['_id'];
                });

//            prn($oldSettings);
//            exit;

//            $main_user = $this->table( 'MainUser' )->get( $this->user()->main_id );
//            $dbsName = $this->table( 'MainDb' )->find( [ 'company_id' => $main_user->company_id ] )->toArray()[0]['dbname'];

            $results = $request->getPost()->toArray();
//            prn($dbSetting);
//            prn($results['modelsIds']);
//            exit;
            $dbSetting->_model_ids = $results['modelsIds'];
//            prn($dbSetting);
            $gw->delete(['_id' => $dbSetting->_id]);
            $gw->insert($dbSetting);
//            prn($results['modelCMPR']);
//            exit;
            foreach ($results['modelCMPR'] as $from => $to) {
                if ($to !== $oldSettings[$from]['to']) {
                    if (isset($oldSettings[$from])) {
                        $gw->delete(['_id' => $oldSettings[$from]['_id']]);
                    }
                    if (strlen($to)) {
                        $modelCmpr = [
                            '_purpose' => 'model_settings',
                            '_models'  => [
                                'to'   => $to,
                                'from' => $from,
                            ],
                        ];
//                    prn($modelCmpr);
                        $gw->insert($modelCmpr);
                    }
                }
            }

//            exit;
            return $this->refresh('Model compare saved',
                $this->url()->fromRoute('model', ['action' => 'index']),
                1);
        }

        $dbAdapter = $this->getServiceLocator()->get($results['from']);

        foreach ($dbSetting['_model_ids'] as $modelName => $idField) {
            $results['previous_ids'][$modelName] = $idField;
        }

        $previousSettings = $gw->find(['_purpose' => 'model_settings']);

        $results['previous_settings'] = [];

        foreach ($previousSettings as $setting) {
            //            $results['previous_settings'][$setting['_models']['to']] = $setting['_models']['from'];
            $results['previous_settings'][$setting['_models']['from']]
                = $setting['_models']['to'];
        }

//        prn($results);
        # get zoho_csv collections list
        $tabs            = $dbAdapter->getDriver()->getConnection()->getDB()
            ->listCollections();
        $results['zoho'] = [];

        foreach ($tabs as $k => $tab) {
            //            $results[ 'zoho' ][ $tab->getName() ] = [ 'value'=> $tab->getName(), 'text'=>$tab->getName() ];
            $tabName           = $tab->getName();
            $results['zoho'][] = $tabName;
            $tempGw            = new MongoGateway($tabName, $dbAdapter);
            $modelExample      = $tempGw->find([])->current();
            foreach ($modelExample as $fieldName => $value) {
                if ($fieldName != '_id') {
                    //                $results['previous_ids'][$tabName] = $fieldName;
                    $results['oldIds'][$tabName][$fieldName]
                        = ['text' => $fieldName, 'value' => $fieldName];
                }
            }
        }
//        prn($results['previous_ids']);

        # and now i want to get our models list

        $config = $this->getServiceLocator()->get('config');
//        prn($config);
        $systemConfig = $config['ModelConfig'];

        $systemConfig['db'] = $this->_gatewayServiceRaw
            ->get('ModelConfig', new ModelConfig())
            ->fetchAll()->toArray();

        $results['wepo'] = [];
        foreach ($systemConfig as $type) {
            foreach ($type as $model) {
                $results['wepo'][$model['model']] = [
                    'value' => $model['model'],
                    'text'  => $model['model']
                ];
            }
        }
//        $results['wepo'] = array_values(array_unique($results['wepo']));
        $results['action'] = 'dbcmpr';

//        prn($results);

        return $results;
    }

    public function refresh($message = null, $tourl = null, $seconds = 0)
    {
        $viewModel = new ViewModel([
            'message' => $message,
            'user'    => $this->user(),
            'toUrl'   => $tourl,
            'seconds' => $seconds,
        ]);

        return $viewModel->setTemplate('wepo/partial/refresh.twig');
    }

    public function modelcmprAction()
    {
        list($db, $gw) = $this->checkPermission();
        $results['title']  = 'Compare models';
        $results['db']     = $db;
        $results['action'] = 'modelcmpr';
        $from              = $this->params('from', null);
        $to                = $this->params('to', null);
        $results['from']   = $from;
        $results['to']     = $to;

        $dbAdapter = $this->getServiceLocator()->get($db);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $results = $request->getPost()->toArray();
//            prn( $results );
////            $gw = new MongoGateway( 'export_wepo_settings', $dbAdapter );
//
            $setting     = $gw->find([
                '_purpose'     => 'model_settings',
                '_models.from' => $results['from']
            ])->current()
                ->getArrayCopy();
            $allowedKeys = array_filter(array_keys($setting), function ($key) {
                return strpos($key, '_') === 0 ? true : false;
            });
            $setting     = array_intersect_key($setting,
                array_flip($allowedKeys));
//
//            $locStack = explode(',', $results['LocStack']);
//            $extStack = explode(',', $results['ExtStack']);
//            $count = count($locStack);
            $modelIdFields = $gw->find(['_purpose' => 'db_settings'])
                ->current()->_model_ids;
//            prn('is id',$modelIdFields);
            $i = 0;
            foreach ($results['modelCMPR'] as $from => $to) {
                $idfield = str_replace(' ', '', strtolower($from));
                if (strlen($from) && strlen($to)) {
                    if (isset($results['idlinks'][$idfield])) {
                        $dbase          = $results['idlinks'][$idfield];
                        $setting[$from] = [
                            'to'   => $to,
                            'from' => isset($results['isStatic'][$idfield])
                                ? $dbase
                                :
                                $dbase . '.' . $modelIdFields[$dbase],
                            'type' => isset($results['isStatic'][$idfield])
                                ? 'static_lookup' : 'lookup',
                        ];
                    } else {
                        $setting[$from] = $to;
                    }
                } elseif (strlen($from)) {
                    $field = implode('_', explode(' ', strtolower($from)));
                    if (isset($results['idlinks'][$idfield])) {
                        $dbase          = $results['idlinks'][$idfield];
                        $setting[$from] = [
                            'to'   => $field,
                            'from' => isset($results['isStatic'][$idfield])
                                ? $dbase
                                :
                                $dbase . '.' . $modelIdFields[$dbase],
                            'type' => isset($results['isStatic'][$idfield])
                                ? 'static_lookup' : 'lookup',
                        ];
                    } else {
                        $setting[$from] = $field;
                    }
                }
                $i++;
            }
//            prn( $setting );
//            exit;
            $gw->update($setting, ['_id' => $setting['_id']]);
//            exit;
//            prn($this->url()->fromRoute( 'model', ['action'=>'index'] ));
//            exit;
            return $this->refresh('Model compare saved',
                $this->url()->fromRoute('model', ['action' => 'index']),
                1);
        }

        $setting = $gw->find([
            '_purpose'     => 'model_settings',
            '_models.from' => $results['from']
        ])->current()
            ->getArrayCopy();

        $results['previous_settings'] = [];
        foreach ($setting as $fieldFrom => $fieldTo) {
            if (strpos($fieldFrom, '_') === false) {
                $results['previous_settings'][$fieldFrom] = $fieldTo;
            }
        }

        $gw = new MongoGateway($from, $dbAdapter);

        $results['zoho'] = array_values(array_diff(
            array_keys($gw->find([])->current()->getArrayCopy()),
            ['_id']
        ));

        # I want to know tables in zoho_csv :)
        # get zoho_csv adapter
        $dbAdapter = $this->getServiceLocator()->get($results['db']);

        # get zoho_csv collections list
        $tabs                   = $dbAdapter->getDriver()->getConnection()
            ->getDB()->listCollections();
        $results['zoho_models'] = [];

        $staticModels = $this->getServiceLocator()
            ->get('ModelFramework\ConfigService')
            ->getConfigDomainSystem('StaticDataSource');
        $staticModels += $this->getServiceLocator()
            ->get('ModelFramework\ConfigService')
            ->getConfigDomainCustom('StaticDataSource');
        $staticModels = array_keys($staticModels);
        foreach ($staticModels as $model) {
            $results['zoho_static_models'][] = [
                'value' => $model,
                'text'  => $model
            ];
        }

        foreach ($tabs as $k => $tab) {
            $results['zoho_models'][] = [
                'value' => $tab->getName(),
                'text'  => $tab->getName()
            ];
        }

        $config             = $this->getServiceLocator()->get('config');
        $systemConfig       = $config['ModelConfig'];
        $systemConfig['db'] = $this->_gatewayServiceRaw
            ->get('ModelConfig', new ModelConfig())
            ->fetchAll()->toArray();

        $results['wepo']['_id'] = ['value' => '_id', 'text' => '_id'];
        foreach ($systemConfig as $type => $modelArray) {
            foreach ($modelArray as $model) {
                if ($model['model'] === $to) {
                    foreach ($model['fields'] as $key => $field) {
                        $results['wepo'][$key] = [
                            'value' => $key,
                            'text'  => $key
                        ];
//                        $results['wepo'][] = $key;
                    }

                    break 2;
                }
            }
        }

//        prn($results);

//        $results = new ViewModel($results);
//        $results->setTemplate('wepo\model\sort.twig');
        return $results;
    }

    private function checkPermission()
    {
        $db = $this->params('db', null);
        if (isset($db)) {
            #get zoho csv adapter and gateway for export settings collection
            $dbAdapterCSV = $this->getServiceLocator()->get($db);
            $gwSrc        = new MongoGateway('export_wepo_settings',
                $dbAdapterCSV);

            $request = $this->getServiceLocator()->get('request');

            if ($request instanceof Request) {
                $gw       = $this->getGatewayServiceVerify()->get('MainUser');
                $login    = $this->params('login', null);
                $mainUser = $gw->findOne(['login' => $login]);
                if ($mainUser
                    && in_array((string)$mainUser->status_id,
                        [Status::NEW_, Status::NORMAL])
                ) {
                    $this->getAuthServiceVerify()->setMainUser($mainUser);
                    $this->getLogicServiceVerify()
                        ->get('signin', $mainUser->getModelName())
                        ->trigger($mainUser);
                }
            } else {
                $mainUser = $this->getAuthServiceVerify()->getMainUser();
            }

            if ($mainUser) {
                $dbsName = $this->getGatewayServiceVerify()->get('MainDb')
                               ->find(['company_id' => $mainUser->company_id])
                               ->toArray()[0]['dbname'];
            }

            if (count($gwSrc->find([
                '_purpose' => 'db_settings',
                '_owner'   => $dbsName
            ]))) {
                return [$db, $gwSrc, $dbAdapterCSV];
            }
        }
        throw new \Exception('You doesn\'t have permission to export this db');
    }

    public function consoleAction()
    {
        $request = $this->getServiceLocator()->get('request');
        if ($request instanceof ConsoleRequest) {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '-1');

            list($results['db'], $gwConvert, $dbAdapterCSV)
                = $this->checkPermission();

            $purpose   = $this->params('purpose', null);
            $model     = $this->params('model', null);
            $logicType = $this->params('logic_type', 'zohoimport');
            switch ($purpose) {
                case 'logic':
                    prn('started logic');
                    $this->triggerLogic($gwConvert, $logicType, $model);
                    prn('end logic');
                    break;
            }
        }
    }

    public function triggerLogic($gwConvert, $logicType, $modelName)
    {
        $adam   = "54c0f83d5d257b7e188db65c";
        $count  = 0;
        $models = $this->getServiceLocator()
            ->get('ModelFramework\GatewayService')
            ->get($modelName)->find(['_id' => $adam]);
        foreach ($models as $model) {
            prn($modelName . '.' . $logicType, ++$count, (string)$model->_id);
            $this->getServiceLocator()->get('ModelFramework\LogicService')
                ->get($logicType, $modelName)
                ->trigger($model);
        }
    }

    public function convertAction()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        //usefull for testing purpose
        $supportedModels = [
            'User',
            'Lead',
            'Patient',
            'Account',
            'Product',
            'Pricebook',
            'Quote',
            'Order',
            'Invoice',
            'QuoteDetail',
            'OrderDetail',
            'InvoiceDetail',
            //            'Task',
            //            'Event'
        ];

        $addTabs = ['Task', 'Event', 'Call', 'Note'];

        list($results['db'], $gwConvert, $dbAdapterCSV)
            = $this->checkPermission();

        $rules = $gwConvert->find(['_purpose' => 'model_settings']);

        $request = $this->getServiceLocator()->get('request');
        if ($request instanceof Request) {

            $purpose   = $this->params('purpose', null);
            $model     = $this->params('model', null);
            $logicType = $this->params('logic_type', 'zohoimport');
//            prn($model,$purpose);
//            exit;
            $supportedModels
                = strtolower($model) == 'all' ? $supportedModels : [$model];
            switch ($purpose) {
                case 'copy':
                    prn('started database copy');
                    $this->copyDatabase($rules, $dbAdapterCSV,
                        strtolower($model) == 'all' ?
                            $supportedModels + $addTabs : [$model]);
                    prn($supportedModels);
                    prn('end database copy');
                    break;
                case 'cross':
                    prn('started cross ids update');
                    $this->setCrossIds($rules, $gwConvert, $supportedModels);
                    prn($supportedModels);
                    prn('end cross ids update');
                    break;
                case 'logic':
                    prn('started logic update');
                    $this->applyLogic($rules, $gwConvert, $supportedModels,
                        $logicType);
                    prn($supportedModels);
                    prn('end logic update');
                    break;
            }
        } else {
//            prn( 'started database copy' );
//            $this->copyDatabase( $rules, $dbAdapterCSV, $supportedModels );
//            prn( 'end database copy' );
//            prn( 'started cross ids update' );
//            $this->setCrossIds( $rules, $gwConvert, $supportedModels );
//            prn( 'end cross ids update' );
//            prn( 'started logic update' );
//            $this->applyLogic( $rules, $gwConvert, $supportedModels );
//            prn( 'end logic update' );
            return $this->refresh('Data converted',
                $this->url()->fromRoute('model', ['action' => 'index']),
                1);
        }
        exit;
    }

    private function copyDatabase($rules, $dbAdapterCSV, $supportedModels)
    {
        $count = 0;
        foreach ($rules as $rule) {
            if ((count($rule) > 3)
                && (in_array($rule['_models']['to'], $supportedModels))
            ) {
                if ( !isset($rule['_models'])
                    || !isset($rule['_models']['from'])
                    || !isset($rule['_models']['to'])
                ) {
                    throw new \Exception('_models property does not set correctly');
                }

                $gwSrc = new MongoGateway($rule['_models']['from'],
                    $dbAdapterCSV);
                $gwTar = $this->getGatewayServiceVerify()
                    ->get($rule['_models']['to']);

                $result = $gwSrc->fetchAll();

                foreach ($result as $srcModel) {
                    prn(++$count, (string)$srcModel->_id);
                    $trgModel = new \ArrayObject($gwTar->model()->toArray(),
                        \ArrayObject::ARRAY_AS_PROPS);
                    foreach ($rule as $k => $f) {
                        if ($k[0] == '_') {
                            continue;
                        }
                        if ( !is_array($f)) {
                            $trgModel[$f] = '' . $srcModel[$k];

                        } else {
                            if ($f['to'] == '_id') {
                                $trgModel['_id_export'] = '' . $srcModel[$k];
                            } else {
                                $trgModel[$f['to'] . '_export']
                                    = '' . $srcModel[$k];
                            }
                        }
                    }

                    $trgModel['status_id'] = new \MongoId(Status::NEW_);
//                    $trgModel->status_id = Status::NEW_;
                    if (empty($trgModel['_id_export'])) {
                        $trgModel['_id_export'] = $srcModel['_id'];
                    }
                    //if you want long but clear copying of db
                    $gwTar->insert($trgModel);
                }
            }
        }
        exit();
    }

    private function setCrossIds(
        $exportSettings,
        $gwConvert,
        $supportedModels
    ) {
        $curDbAdapter = $this->getServiceLocator()->get('wepo_company');

        #wigh lookup settings
//        prn($supportedModels);
        foreach ($exportSettings->toArray() as $exportSetting) {
            if (in_array($exportSetting['_models']['to'],
                $supportedModels)) {
                $curWepoModelName = $exportSetting['_models']['to'];

                $lookupFields = [];
                array_walk($exportSetting,
                    function ($field, $key) use (&$lookupFields) {
                        if (is_array($field) && isset($field['type'])) {
                            $lookupFields[$key] = $field;
                        }
                    });

                $curWepoModelGw
                    = new MongoGateway($this->getModelServiceVerify()->get($curWepoModelName)
                    ->getTableName(), $curDbAdapter);

                $curWepoModels = $curWepoModelGw->fetchAll();
                $curWepoModels = $curWepoModels->toArray();
//                prn($curWepoModels->toArray());
//                prn($curWepoModels->getDataSource()->getResource()->snapshot());
//                prn($curWepoModels);
//                exit;
                prn($curWepoModelName);
                prn(count($curWepoModels));
//                prn($curWepoModels->getArrayObjectPrototype());
//                exit;

                $count = 0;
                foreach ($curWepoModels as $curWepoModel) {

                    $curWepoModel = new \ArrayObject($curWepoModel,
                        \ArrayObject::ARRAY_AS_PROPS);
//                    $temp->exchangeArray($curWepoModel);
//                    $curWepoModel
//                    prn( ++$count, (string) $curWepoModel['_id']);
                    prn(++$count, (string)$curWepoModel->_id);
//                    exit;
                    foreach ($lookupFields as $fromField => $toField) {
                        switch ($toField['type']) {
                            case 'lookup':
//                                prn( 'lookup', $toField );
                                $toField['from'] = explode('.',
                                    $toField['from']);
                                $extExportModel  = $toField['from'][0];
                                $extExportField  = $toField['from'][1];
                                $curWepoField    = $toField['to'];

                                //activity check block started
//                                if (in_array( $curWepoModelName, $activityModels ) &&
//                                    ( isset( $activitySettings[ $fromField ] ) )
//                                ) {
//                                    $curWepoModel->$activitySettings[ $fromField ][ 'type_field' ] =
//                                        $activitySettings[ $fromField ][ 'type_id' ];
//
//                                    $extExportModel = $toField[ 'from' ][ 0 ];
//                                    $extExportField = $toField[ 'from' ][ 1 ];
//                                    $curWepoField   = $activitySettings[ $fromField ][ 'target_field' ];
//                                }
//                                //activity check block finished

                                if ($exportSetting['_models']['from']
                                    !== $extExportModel
                                ) {
                                    array_walk($exportSettings->toArray(),
                                        function (&$extSetting) use (
                                            $extExportModel,
                                            $extExportField,
                                            &$extWepoModel,
                                            &$extWepoField
                                        ) {
                                            if ($extSetting['_models']['from']
                                                === $extExportModel
                                            ) {
                                                $extWepoModel
                                                    = $extSetting['_models']['to'];
                                                $extWepoField
                                                    = $extSetting[$extExportField]['to'];
                                            }
                                        }
                                    );
                                    if (isset($extWepoModel)
                                        && isset($extWepoField)
                                    ) {
                                        $extWepoModelGw
                                            = new MongoGateway($this->getModelServiceVerify()->get($extWepoModel)
                                            ->getTableName(),
                                            $curDbAdapter);
                                        $sourceWepoModel
                                            = $extWepoModelGw->find([
                                            $extWepoField .
                                            '_export' => $curWepoModel->{
                                            $curWepoField . '_export'},
                                        ]);
                                        if (count($sourceWepoModel) == 1) {
                                            $sourceWepoModel
                                                = $sourceWepoModel->current();
                                            $curWepoModel->{$curWepoField .
                                            '_id'}
//                                            $curWepoModel[$curWepoField. '_id']
                                                = $sourceWepoModel->$extWepoField;
                                        }
                                    }
                                }
                                break;
                            case 'static_lookup':
//                                prn( 'static_lookup' );
                                $extExportModel = $toField['from'];
                                $staticData     = $this->getServiceLocator()
                                    ->get('ModelFramework\ConfigService')
                                    ->get('StaticDataSource',
                                        $extExportModel,
                                        new StaticDataConfig());
                                $extExportField
                                                     = $staticData->attributes['select_field'];
                                $staticData          = $staticData->options;
                                $curWepoField        = $toField['to'];
                                $curModelIdFieldData = $curWepoModel->{
                                $curWepoField . '_export'};

                                array_walk($staticData,
                                    function ($value, $key) use (
                                        &$sValue,
                                        $extExportField,
                                        $curModelIdFieldData
                                    ) {
                                        if ($value[$extExportField]
                                            == $curModelIdFieldData
                                        ) {
                                            $sValue = $key;
                                        }
                                    });

                                $curWepoModel->{$curWepoField . '_id'}
                                    = $sValue;
//                                prn($curWepoModel);
//                                prn( $staticData, $extExportModel, $curWepoField, $extExportField );
//                                exit;
                                break;
                        }
                    }
//                    $curWepoModelGw->delete( [ '_id' => $curWepoModel->_id ] );
//                    $curWepoModelGw->insert( $curWepoModel );
                    $curWepoModelGw->update($curWepoModel,
                        ['_id' => $curWepoModel->_id]);
//                    prn('after', $curWepoModel);
//                    exit;
                }
//                exit;

//                prn($lookupFields);
//                exit;
            }
        }
    }

    private function applyLogic(
        $exportSettings,
        $gwConvert,
        $supportedModels,
        $logicTitle
    ) {
//        register_shutdown_function( "fatal_handler" );
        //        $supportedModels = [
//            'Lead',
//            'User',
//            'Patient',
//            'Account',
//            'Product',
//            'Pricebook',
//            'Quote',
//            'Order',
//            'Invoice'
//        ];
//        prn($exportSettings->toArray());
        $curDbAdapter   = $this->getServiceLocator()->get('wepo_company');
        $exportSettings = $exportSettings->toArray();
        prn('sort problem could be');
//        prn($exportSettings);
        usort($exportSettings, function ($a, $b) use ($supportedModels) {
            $aOrder = array_search($a['_models']['to'], $supportedModels);
            $bOrder = array_search($b['_models']['to'], $supportedModels);
            if ($aOrder == $bOrder) {
                return 0;
            }

            return $aOrder < $bOrder ? -1 : 1;
//            if ($a[ '_order' ] == $b[ '_order' ]) {
//                return 0;
//            }
//
//            return ( $a[ '_order' ] < $b[ '_order' ] ) ? -1 : 1;
        });
//        prn($exportSettings);

        foreach ($exportSettings as $setting) {
            if (in_array($setting['_models']['to'], $supportedModels)) {
                //                prn( $setting[ '_models' ][ 'to' ] );

                $curWepoModelGw
                    = new MongoGateway($this->getModelServiceVerify()->get($setting['_models']['to'])
                    ->getTableName(), $curDbAdapter);

                $data = $curWepoModelGw->fetchAll();
                $data = $data->toArray();

                $gw = $this->getGatewayServiceVerify()
                    ->get($setting['_models']['to']);
//                $data = $gw->find( [ ] );
                prn($setting['_models']['to']);
                $count = 0;
                foreach ($data as $model) {
                    prn(++$count, (string)$model['_id']);

                    $copyData = array_filter($model,
                        function ($val) { return !empty($val); });
                    $id       = $model['_id'];
                    $model    = $this->getModelServiceVerify()->get($setting['_models']['to']);
                    $model->exchangeArray($copyData);
                    $model->_id = $id;
//                    prn($model);
//                    exit;

//                    if($setting[ '_models' ][ 'to' ]=='OrderDetail')
//                    {
//                        prn($model,$copyData);
//                    }
//                    prn($model);
//                    exit;
//                    $this->serviceLocator->get( 'ModelFramework\LogicService' )->trigger( 'zohoimport', $model );
                    try {
                        $this->serviceLocator->get('ModelFramework\LogicService')
                            ->get($logicTitle,
                                $model->getModelName())
                            ->trigger($model);
                    } catch (\Exception $ex) {
                        prn('Exception', $ex->getMessage());
                    }
//                    prn( $model );
//                    exit;
                    $gw->save($model);
//                    exit;
                }
            }
        }
//        prn($gwConvert);
    }

///////////////////////////////HACKS FOR NON STANDART MODELS/////////////////////////////

///////////////////////////////HACKS NOTES///////////////////////////////////////////
//      hack activity must be applied after copy of Activity models (Task, Event, Call)
//
//
//
//      hack productlink for QuoteDetail, OrderDetail, InvoiceDetail
//      should be applied after Product logic and before logic of .+Details
//
//
//
//
//////////////////////////////////////////////////////////////////////////////////////

    public function hacksAction()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        list($results['db'], $gwConvert, $dbAdapterCSV)
            = $this->checkPermission();
        $action = $this->params('hackaction', null);
        $model  = $this->params('model', null);

        if (isset($model) && isset($action)) {
//            prn('heare');
//            exit;
            prn('HACK ACTION STARTED');
            $this->$action($model);
            prn('HACK ACTION FINISHED');
        } else {
            throw new \Exception('Wrong params');
        }

        exit;
    }

    public function productlink($modelName)
    {
        $allowedModels = [
            'OrderDetail',
            'QuoteDetail',
            'InvoiceDetail'
        ];

        if (in_array($modelName, $allowedModels)) {
            $modelGW     = new MongoGateway($modelName,
                $this->getServiceLocator()->get('wepo_company'));
            $productGW   = $this->getGatewayServiceVerify()->get('Product');
            $pricebookGW = $this->getGatewayServiceVerify()
                ->get('PricebookDetail');
            $models      = $modelGW->fetchAll();
            prn($modelName);
            $count = 0;
            foreach ($models as $model) {
                prn(++$count);
                prn((string)$model->_id);
//                prn($model->product_id);
                $product
                    = $productGW->find(['_id_export' => $model->product_id])
                    ->current();
//                prn($product->_id);
                $pricebookdetail = $pricebookGW->find([
                    'pricebook_id' => 'a00000000000000000000001',
                    'product_id'   => $product->_id
                ])
                    ->current();
//                prn($pricebookdetail->_id);
                $model->pricebook_detail_id = $pricebookdetail->_id;
                $modelGW->update($model, ['_id' => $model->_id]);
            }

        } else {
            throw new \Exception('Not allowed hack for selected model');
        }

    }


    public function activity($modelName)
    {
        $allowedModels = [
            'Task',
            'Event',
            'Call'
        ];

        $activitySettings = [
            'contact_name' => [
                'target_model' => 'Patient',
            ],
            'related_to'   => [
                'target_model' => 'Lead',
            ],
        ];

        $lookupFields = [
            'owner',
            'changer',
            'creator',
            'target'
        ];

        $dbAdapter = $this->getServiceLocator()->get('wepo_company');

        $activityGW = new MongoGateway('Activity', $dbAdapter);
        $activities = $activityGW->find(['type' => $modelName])->toArray();

        prn($modelName);

        $count = 0;
        foreach ($activities as $activity) {
            $resModelName = $modelName;
            prn(++$count, (string)$activity['_id']);
            $flag = true;
            foreach ($activitySettings as $field => $setting) {
                if ( !empty($activity[$field])) {
                    $resModelName
                                             =
                        $resModelName . $setting['target_model'];
                    $activity
                                             = array_merge($this->getModelServiceVerify()
                        ->get($resModelName)->toArray(),
                        $activity);
                    $activity                = new \ArrayObject($activity,
                        \ArrayObject::ARRAY_AS_PROPS);
                    $activity->target_export = $activity->$field;
                    $flag                    = false;
                    break;
                }
            }
            if ($flag) {
                $resModelName = $resModelName . 'Lead';
                $activity     = array_merge(

                    $this->getModelServiceVerify()
                        ->get($resModelName)->toArray(),
                    $activity);
                $activity     = new \ArrayObject($activity,
                    \ArrayObject::ARRAY_AS_PROPS);
            }

            $settings = $this->getServiceLocator()
                ->get('ModelFramework\ConfigService')
                ->getByObject($resModelName,
                    new ModelConfig())->fields;

            foreach ($lookupFields as $field) {
                $setting      = $settings[$field];
                $extModelName = $setting['model'];
                $gw           = new MongoGateway($extModelName, $dbAdapter);
//                    if ( $field=='target')
                if ( !isset($activity->{$field . '_export'})) {
                    continue;
//                    prn($field, $extModelName, $activity);
//                    exit();
                }

                $model = $gw->find([
                    '_id_export' => $activity->{$field . '_export'}
                ])->current();

                if (isset($model)) {
                    $activity->{$field . '_id'} = $model->_id;
                }
            }
            $activityGW->update($activity, ['_id' => $activity->_id]);
            $temp     = $this->getModelServiceVerify()->get($resModelName);
            $activity = (array)$activity;
            $temp->exchangeArray($activity);
            try {
                $this->serviceLocator->get('ModelFramework\LogicService')
                    ->get('zohoimport', $resModelName)
                    ->trigger($temp);
            } catch (\Exception $ex) {
                prn('Exception', $ex->getMessage());
            }
            $this->getGatewayServiceVerify()->get($resModelName)->save($temp);
        }
    }

    public function note($modelName)
    {
        $searchModels = [
            'Patient',
            'Lead'
        ];

        $lookupFields = [
            'owner',
            'changer',
            'creator',
            'lead',
            'patient'
        ];

        $dbAdapter = $this->getServiceLocator()->get('wepo_company');

        $noteGW = new MongoGateway($modelName, $dbAdapter);
        $notes  = $noteGW->find([])->toArray();

//        prn( $modelName );

        $count = 0;
        foreach ($notes as $note) {
            $resModelName = $modelName;
            $flag         = true;
            prn(++$count, (string)$note['_id']);
            if ( !empty($note['target'])) {
                foreach ($searchModels as $extModelName) {
                    $targetGW = new MongoGateway($extModelName, $dbAdapter);
                    $extModel
                              = $targetGW->find(['_id_export' => $note['target']])
                        ->current();
                    if (isset($extModel)) {
                        $flag         = false;
                        $resModelName = $resModelName . $extModelName;
                        $note
                                      = array_merge($this->getModelServiceVerify()
                            ->get($resModelName)->toArray(),
                            $note);
                        $note
                                      = new \ArrayObject($note,
                            \ArrayObject::ARRAY_AS_PROPS);
                        $note->{strtolower($extModelName) . '_export'}
                                      = $note->target;
                        break;
                    }
                }
            }
            if ($flag) {
                $resModelName = $resModelName . 'Lead';
                $note         = array_merge($this->getModelServiceVerify()
                    ->get($resModelName)->toArray(),
                    $note);
                $note         = new \ArrayObject($note,
                    \ArrayObject::ARRAY_AS_PROPS);
            }
//            prn( $resModelName, $flag );

            $settings = $this->getServiceLocator()
                ->get('ModelFramework\ConfigService')
                ->getByObject($resModelName,
                    new ModelConfig())->fields;

            foreach ($lookupFields as $field) {
                if (isset($note->{$field . '_export'})) {
                    $setting      = $settings[$field];
                    $extModelName = $setting['model'];
                    $gw           = new MongoGateway($extModelName, $dbAdapter);
                    $model        = $gw->find([
                        '_id_export' => $note->{$field . '_export'}
                    ])->current();
                    if (isset($model)) {
                        $note->{$field . '_id'} = $model->_id;
                    }
                }
            }
            $noteGW->update($note, ['_id' => $note->_id]);
            $temp = $this->getModelServiceVerify()->get($resModelName);
            $note = (array)$note;
            $temp->exchangeArray($note);
            try {
                $this->serviceLocator->get('ModelFramework\LogicService')
                    ->get('zohoimport', $resModelName)
                    ->trigger($temp);
            } catch (\Exception $ex) {
                prn('Exception', $ex->getMessage());
            }
            $temp->_id = null;
//            prn($resModelName,$temp);
//            exit;
            $this->getGatewayServiceVerify()->get($resModelName)->save($temp);
//            exit;
        }
    }

    public function importConfAction()
    {

    }

}
