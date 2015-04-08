<?php
/**
 * Class View
 *
 * @package ModelFramework\ViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService;

use ModelFramework\AclService\AclDataModel;
use ModelFramework\AclService\AclServiceAwareInterface;
use ModelFramework\AclService\AclServiceAwareTrait;
use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelAwareInterface;
use ModelFramework\DataModel\DataModelAwareTrait;
use ModelFramework\FileService\FileServiceAwareInterface;
use ModelFramework\FileService\FileServiceAwareTrait;
use ModelFramework\FilesystemService\FilesystemServiceAwareInterface;
use ModelFramework\FilesystemService\FilesystemServiceAwareTrait;
use ModelFramework\ListParamsService\ListParamsServiceAwareInterface;
use ModelFramework\ListParamsService\ListParamsServiceAwareTrait;
use ModelFramework\PDFService\PDFServiceAwareInterface;
use ModelFramework\PDFService\PDFServiceAwareTrait;
use ModelFramework\GatewayService\GatewayAwareInterface;
use ModelFramework\GatewayService\GatewayAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\LogicService\LogicServiceAwareInterface;
use ModelFramework\LogicService\LogicServiceAwareTrait;
use ModelFramework\ModelService\ModelConfig\ParsedModelConfigAwareInterface;
use ModelFramework\ModelService\ModelConfig\ParsedModelConfigAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\FormService\FormServiceAwareInterface;
use ModelFramework\FormService\FormServiceAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use ModelFramework\Utility\Arr;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;
use ModelFramework\ViewBoxService\ResponseAwareInterface;
use ModelFramework\ViewBoxService\ResponseAwareTrait;
use ModelFramework\ViewService\ViewConfig\ViewConfigAwareInterface;
use ModelFramework\ViewService\ViewConfig\ViewConfigAwareTrait;
use Zend\View\Model\ViewModel as ZendViewModel;
use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\TwigService\TwigServiceAwareInterface;
use ModelFramework\TwigService\TwigServiceAwareTrait;

class View
    implements ViewInterface, ViewConfigAwareInterface,
               ParsedModelConfigAwareInterface,
               ModelServiceAwareInterface, GatewayAwareInterface,
               ParamsAwareInterface, GatewayServiceAwareInterface,
               FormServiceAwareInterface, FileServiceAwareInterface,
               AclServiceAwareInterface, AuthServiceAwareInterface,
               LogicServiceAwareInterface, PDFServiceAwareInterface,
               QueryServiceAwareInterface, ConfigServiceAwareInterface,
               \SplSubject, ResponseAwareInterface, DataModelAwareInterface,
		        TwigServiceAwareInterface, FilesystemServiceAwareInterface, ListParamsServiceAwareInterface
{

    use ViewConfigAwareTrait, ParsedModelConfigAwareTrait, GatewayAwareTrait, ParamsAwareTrait,
        GatewayServiceAwareTrait, ModelServiceAwareTrait, FormServiceAwareTrait, PDFServiceAwareTrait,
        AuthServiceAwareTrait, LogicServiceAwareTrait, QueryServiceAwareTrait, FileServiceAwareTrait,
        AclServiceAwareTrait, ConfigServiceAwareTrait, ResponseAwareTrait, DataModelAwareTrait,
	TwigServiceAwareTrait, FilesystemServiceAwareTrait, ListParamsServiceAwareTrait;

    protected $allowed_observers
        = [
            'RowCountObserver',
            'ListObserver',
            'ViewObserver',
            'FormObserver',
            'ConvertObserver',
            'RecycleObserver',
            'FieldObserver',
            'EnsureIndexObserver',
            'UserObserver',
            'ListDetailsObserver',
            'UploadObserver',
            'WidgetObserver',
            'ParamsObserver',
            'AttachObserver',
            'DownloadObserver',
            'LogicObserver',
            'HTMLObserver',
            'SignInObserver',
            'SignOutObserver',
            'SignUpObserver',
            'MailSendObserver',
            'PDFObserver',
            'ListLiteObserver',
            'MailTplObserver',
            'CloneObserver',
            'ConstructPatternMenuObserver',
        ];
    protected $observers = [];
    protected $_name = '';
    private $_data = [];
    private $_redirect = null;
    private $_isAllowed = true;

    public function setName($name )
    {
        $this->_name = $name;
        return $this;
    }

    public function getName( )
    {
        return $this->_name;
    }

    public function getRedirect()
    {
        return $this->_redirect;
    }

    public function setRedirect($redirect)
    {
        $this->_redirect = $redirect;
    }

    public function hasRedirect()
    {
        if ( !empty($this->_redirect)) {
            return true;
        }

        return false;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setData(array $data)
    {
        $this->_data = Arr::merge($this->_data, $data);
    }

    public function init()
    {
        foreach (
            $this->getViewConfigVerify()->observers as $observer =>
            $obConfig
        ) {
            if (is_numeric($observer)) {
                $observer = $obConfig;
                $obConfig = null;
            }
            if ( !in_array($observer, $this->allowed_observers)) {
                throw new \Exception($observer . ' is not allowed in ' .
                    get_class($this));
            }
            $observerClassName
                  = 'ModelFramework\ViewService\Observer\\' . $observer;
            $_obs = new $observerClassName();
            if ( !empty($obConfig) && $_obs instanceof ConfigAwareInterface) {
                $_obs->setRootConfig($obConfig);
            }
            $this->attach($_obs);
        }
    }

    public function attach(\SplObserver $observer)
    {
        $this->observers[] = $observer;
    }

    public function detach(\SplObserver $observer)
    {
        $key = array_search($observer, $this->observers);
        if ($key) {
            unset($this->observers[$key]);
        }
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function fields()
    {
        return $this->getViewConfigVerify()->fields;
    }

    public function process()
    {
        $this->checkPermissions();
        $this->setDataFields();
        $this->notify();
        $this->checkPermissions();
        if ( !$this->isAllowed()) {
            return $this;
        }
        return $this;
    }

    protected function checkPermissions()
    {
        $model           = $this->getAclDataModelVerify();
        $user            = $this->getUser();
        $permittedConfig = $this->getViewConfigVerify();
        $modePermissions = $model->getAclConfigVerify()->modes;
        $modelAcl        = $model->getDataModelVerify()->_acl;
        if ( !empty($modelAcl)) {
            foreach ($modelAcl as $acl) {
                if ($acl['role_id'] == (string)$user->id()
                    || $acl['role_id'] == (string)$user->role_id
                ) {
                    if ( !isset($acl['modes'])) {
                        continue;
                    }
                    foreach ($acl['modes'] as $mode) {
                        if ( !in_array($mode, $modePermissions)) {
                            $modePermissions[] = $mode;
                        }
                    }
                }
            }
        }
        if ( !is_array($modePermissions)
            ||
            !in_array($permittedConfig->mode, $modePermissions)
        ) {
            $this->denyPermission();
            return false;
        }
        foreach (['actions', 'links'] as $resource) {
            foreach ($permittedConfig->$resource as $action => $link) {
                if ( !in_array($action, $modePermissions)) {
                    unset($permittedConfig->{$resource}[$action]);
                }
            }
        }
        $this->setViewConfig($permittedConfig);

        return true;
    }

    public function getAclDataModelVerify()
    {
        if ($this->getDataModel() !== null) {
            return $this->getDataModel();
        }

        $model = $this->getGatewayVerify()->model();
        if ($model == null || !$model instanceof AclDataModel) {
            throw new \Exception('AclDataModel does not set in Gateway ' .
                $this->getGatewayVerify()->getTable());
        }

        return $model;
    }

    public function getUser()
    {
        return $this->getAuthServiceVerify()->getUser();
    }

    protected function denyPermission()
    {
        $this->setPermission(false);
    }

    protected function setPermission($permission)
    {
        $this->_isAllowed = $permission;
    }

    public function setDataFields()
    {
        $viewConfig            = $this->getViewConfigVerify();
        $result                = [];
        $result['title']       = $viewConfig->title;
        $result['template']    = $viewConfig->template;
        $result['fields']      = $viewConfig->fields;
        $result['actions']     = $viewConfig->actions;
        $result['links']       = $viewConfig->links;
        $result['labels']      = $this->labels();
        $result['modelname']   = strtolower($viewConfig->model);
        $result['queryparams'] = [];
        $result['user']        = $this->getUser();
        $result['saurlhash']   = $this->generateLabel();
        $result['saurl']       = '?back=' . $result['saurlhash'];
        $result['saurlback']   = $this->getSaUrlBack($this->getParams()
            ->fromQuery('back',
                'home'));
        $result['listparamshash'] = $this->getListParamsServiceVerify()->getHash($result['modelname'], $this->getParams());
        $result['listparams'] = $this->getListParamsServiceVerify()->getListParams($this->getParams()->fromQuery('lp',null));
        $this->setData($result);
    }

    public function labels()
    {
        return $this->getParsedModelConfigVerify()->labels;
    }

    public function generateLabel()
    {
        $saUrlGateway = $this->getGatewayServiceVerify()->get('SaUrl');
        $saUrl        = $saUrlGateway->model();
        $saUrl->url   = $this->getParams()->getController()->getRequest()
            ->getServer('REQUEST_URI');
        $checkUrl     = $saUrlGateway->findOne(['url' => $saUrl->url]);
        if ($checkUrl) {
            return $checkUrl->label;
        } else {
            if (strlen($saUrl->url)) {
                $saUrl->label = md5($saUrl->url);
            }
            $i = 0;
            while (++$i < 6
                && $saUrlGateway->find(['label' => $saUrl->label])
                    ->count()) {
                $saUrl->label
                    = md5($saUrl->url . time() . (rand() * 10000));
            }
            if ($i >= 6) {
                return '/';
            }
            try {
                $saUrlGateway->save($saUrl);
            } catch (\Exception $ex) {
                $saUrl->label = '/';
            }

            return $saUrl->label;
        }
    }

    public function getSaUrlBack($backHash)
    {
        $saUrlBack = $this->getGatewayServiceVerify()->get('SaUrl')
            ->find(['label' => $backHash]);
        if ($saUrlBack->count() > 0) {
            $saUrlBack = $saUrlBack->current()->url;
        } else {
            $saUrlBack = '/';
        }

        return $saUrlBack;
    }

    public function isAllowed()
    {
        return $this->_isAllowed;
    }

    public function getBackUrl()
    {
        $url   = null;
        $saUrl = $this->getParams()->fromPost('saurl', []);
        if (isset($saUrl['back'])) {
            $url = $this->getSaurlBack($saUrl['back']);
        }

        return $url;
    }

    public function refresh($message = null, $toUrl = null, $seconds = 0)
    {
        $viewModel = new ZendViewModel([
            'message' => $message,
            'user'    => $this->getUser(),
            'toUrl'   => $toUrl,
            'seconds' => $seconds,
        ]);

        return $viewModel->setTemplate('wepo/partial/refresh.twig');
    }

    protected function clearData()
    {
        $this->_data = [];
    }
}
