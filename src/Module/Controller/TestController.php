<?php

namespace Wepo\Controller;

use ModelFramework\AclService\AclConfig\AclConfig;
use ModelFramework\AclService\AclServiceAwareInterface;
use ModelFramework\AclService\AclServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\FormService\FormServiceAwareInterface;
use ModelFramework\FormService\FormServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use Wepo\Model\Role;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorInterface;

class TestController extends AbstractActionController
    implements FormServiceAwareInterface,
               AclServiceAwareInterface,
               ConfigServiceAwareInterface, ModelServiceAwareInterface
{

    use FormServiceAwareTrait,
        AclServiceAwareTrait,
        ConfigServiceAwareTrait, ModelServiceAwareTrait;

    private $_mcp0 = null;

    public function indexAction()
    {
        return [];
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        parent::setServiceLocator($serviceLocator);
        if ( !$serviceLocator instanceof \Zend\Mvc\Controller\ControllerManager
        ) {
            $this->setConfigService($serviceLocator->get('ModelFramework\ConfigService'));
            $this->setModelService($serviceLocator->get('ModelFramework\ModelService'));
            $this->setFormService($serviceLocator->get('ModelFramework\FormService'));
            $this->setAclService($serviceLocator->get('ModelFramework\AclService'));
//          $this->_mcp0 = $serviceLocator->get('ModelFramework\ModelConfigParser0Service');
        }
    }

    public function formsAction()
    {
        $test = $this->getAclServiceVerify()->getAclDataModel('Test');
        $form = $this->getFormServiceVerify()->getForm($test, 'insert');

        prn($test, $form, $form->getConfig());

        return ['form' => $form];
    }

    public function fieldsAction()
    {

        $models = $this->getModelServiceVerify()
            ->getAllModelNames('all');
        $fields = [];
        foreach (
            $models as
            $model
        ) {
            $t = $this->getModelServiceVerify()
                ->getModelConfig($model);
            foreach ($t->fields as $field => $conf) {
                $fields[$model][] = $field;
            }
        }

        return ['models' => $models, 'fields' => $fields];
    }

    public function permsAction()
    {
        $acls   = [];
        $models = $this->getModelServiceVerify()
            ->getAllModelNames('all');
        $i      = 0;
        foreach (Role::ids() as $role_id) {
            foreach ($models as $model) {
                $t = $this->getModelServiceVerify()
                    ->getModelConfig($model);

                $acl         = $this->getAclData($model, $role_id);
                $acl->key    = $model . '.' . Role::getTitle($role_id);
                $aclFields   = $acl->fields;
                $acl->fields = [];
                foreach ($t->fields as $field => $conf) {
                    if (substr($field, 0, 1) == '_') {
                        continue;
                    }
                    $acl->fields[$field] = isset($aclFields[$field])
                        ? $aclFields[$field] : 'read';
                }

                $acls[] = $acl->toArray();

            }
        }

        return ['acls' => $acls];
    }

    /**
     * @param $modelName
     * @param $role_id
     *
     * @return AclConfig|\ModelFramework\DataModel\DataModelInterface|null
     * @throws \Exception
     */
    private function getAclData($modelName, $role_id)
    {
        $acl = $this->getConfigServiceVerify()
            ->getByObject(
                $modelName . '.' . Role::getTitle($role_id),
                new AclConfig()
            );
        if ($acl == null) {
            if ($role_id == Role::GUEST) {
                return new AclConfig();
            }
            return new AclConfig();
        }

        return $acl;
    }

}
