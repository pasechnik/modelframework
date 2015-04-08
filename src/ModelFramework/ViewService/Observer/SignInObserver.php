<?php
/**
 * Class SignOutObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\AclService\AclDataModel;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;
use ModelFramework\ViewService\View;
use Wepo\Form\SignInForm;
use Wepo\Model\Status;

class SignInObserver
    implements \SplObserver, ConfigAwareInterface, SubjectAwareInterface
{

    use ConfigAwareTrait, SubjectAwareTrait;

    private $_aclModel = null;
    private $_model = null;

    /**
     * @param \SplSubject|View $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);
        $subject->getLogicServiceVerify()->setParams($subject->getParams());
        $form = $this->initForm();
        $this->process($form,
            $subject->getModelServiceVerify()->get('MainUser'));
        $form->prepare();
        $subject->setData(['form' => $form]);
    }

    public function getModel()
    {
        if ($this->_aclModel !== null) {
            return $this->_aclModel;
        }

        return $this->_model;
    }

    public function getModelData()
    {
        return $this->_model;
    }

    public function initModel()
    {
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        $query
                    = $subject->getQueryServiceVerify()
            ->get($viewConfig->query)
            ->setParams($subject->getParams())
            ->process();
        $data       = $subject->getData();
        if (isset($data['model'])
            && $data['model'] instanceof DataModelInterface
        ) {
            $model = $data['model'];
        } else {
            if ($viewConfig->mode == 'insert') {
                $model = $subject->getGateway()->model();
//                $model = $query->setDefaults( $model );
            } elseif ($viewConfig->mode == 'update') {
                $model = $subject->getGateway()->findOne($query->getWhere());
                if ($model == null) {
                    throw new \Exception('Data is not accessible');
                }
            } else {
                throw new \Exception("Wrong mode  '" . $viewConfig->mode .
                    "' in  " . $viewConfig->key .
                    ' View Config for the ' . get_class());
            }
        }
        if ($model instanceof AclDataModel) {
            $this->_aclModel = $model;
            $this->_model    = $this->_aclModel->getDataModel();
        }
        $subject->getLogicServiceVerify()
            ->get('setDefaults', $model->getModelName())
            ->trigger($this->_model);
        return $this->_model;
    }

    public function setModel(DataModelInterface $model)
    {
        if ($this->_aclModel !== null
            && $this->_aclModel instanceof AclDataModel
        ) {
            $this->_aclModel->setDataModel($model);
            $model = $this->_aclModel;
        }
        $this->getSubject()->setData(['model' => $model]);
        return $model;
    }

    public function initForm()
    {
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        $form       = new SignInForm();
        $form->setAttribute('method', 'post');
        $form->setRoute('common');
        $form->setActionParams([
            'data' => 'signin',
            'view' => $viewConfig->mode
        ]);
        return $form;
    }

    /**
     * @param $form
     * @param $mainUser
     */
    public function process($form, $mainUser)
    {
        /**
         * @var View $subject
         */
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        $results    = [];
        $results['redirect_url'] = $subject->getParams()->getController()->url()
            ->fromRoute('common', ['data' => 'dashboard']);
        $request    = $subject->getParams()->getController()->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $credentials = [];
                $_data       = $form->getData();
                if (isset($_data['fields'])) {
                    $credentials = $_data['fields'];
                }
                unset($_data);
                # :FIXME: implement crypt function for all of password staff
                $credentials ['password']
                       = md5($credentials ['password']);
                $gw    = $subject->getGatewayServiceVerify()
                    ->getGateway($mainUser->getModelName(),
                        $mainUser);
                $check = $gw->find($credentials);
                if ($check->count()) {
                    $mainUser = $check->current();

                    if (in_array((string)$mainUser->status_id,
                        [Status::NEW_, Status::NORMAL])) {
                        $subject->getAuthServiceVerify()
                            ->setMainUser($mainUser);
                        $subject->getLogicServiceVerify()
                            ->get('signin', $mainUser->getModelName())
                            ->trigger($mainUser);

                        $url = $subject->getParams()->getController()->url()
                            ->fromRoute('common', ['data' => 'dashboard']);
                        $results['good_credentials'] = true;
                        unset($results['redirect_url']);
                        $results['redirect_url'] = $url;
//                        prn($results);
//                        exit();
                        $subject->setRedirect($subject->refresh('You have been authorized',
                            $url));
                        return;
                    } else {
                        $results['message']
                            = 'Your account blocked or deleted. Please contact administrator.';
                        $results['good_credentials'] = false;
                    }
                } else {
                    $results['message']
                        = 'There is an error with your Login/Password combination. Please try again.';
                    $results['good_credentials'] = false;
                }
            }
        }
        $subject->setData($results);
    }
}
