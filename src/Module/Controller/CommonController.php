<?php

namespace Wepo\Controller;

use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\ModelService\ModelConfig\ModelConfig;
use ModelFramework\ViewBoxService\ViewBoxServiceAwareInterface;
use ModelFramework\ViewBoxService\ViewBoxServiceAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorInterface;

class CommonController extends AbstractActionController
    implements ViewBoxServiceAwareInterface
{

    use ViewBoxServiceAwareTrait, ConfigServiceAwareTrait;

    private $_twigenv = null;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        parent::setServiceLocator($serviceLocator);
        if ( !$serviceLocator instanceof \Zend\Mvc\Controller\ControllerManager
        ) {
            $query
                = $serviceLocator->get('ModelFramework\QueryServiceSource');
            $query->setParams($this->Params());
            $serviceLocator->get('ModelFramework\ViewService');
            $this->setViewBoxService($serviceLocator->get('ModelFramework\ViewBoxService'));
            $this->setConfigService($serviceLocator->get('ModelFramework\ConfigService'));

            $this->_twigenv = $serviceLocator->get('Twig_Environment');

        }
    }

    public function indexAction()
    {
//        try {
        $data = $this->params()->fromRoute('data', 'dashboard');
        $view = $this->params()->fromRoute('view', 'list');
        $viewBox
              = $this->getViewBoxServiceVerify()->get(ucfirst($data) . '.' .
            $view);
        $viewBox->setParams($this->params());
        $viewBox->chooseStrategy('html');

        /* 1. set Twig Loader String to the Twig Environment */

//        $this->_twigenv = clone $this->_twigenv;
//        $this->_twigenv->setLoader(clone $this->_twigenv->getLoader());
//        $this->_twigenv->getLoader()->addLoader(new \Twig_Loader_String());
   //     prn($this->_twigenv);

        /* AND */
        $template_str  = '<i>{{user}}</i>';
        $template_data = ['user' => 'USSSSER'];

        /* 2.1. get render result */
//        $response = $this->_twigenv->loadTemplate($template_str)
//            ->render($template_data);
//        prn($response);

        /* OR */
        /* 2.2. or return ViewModel with string template */
//        $viewModel = new ViewModel($template_data);
//        return $viewModel->setTemplate($template_str);

        $viewBox->process();
        $res = $viewBox->output();


//        throw new \Exception('zzzz ');

//        } catch ( \Exception $e ) {
//            $viewModel = new ViewModel( array(
//                'message' => $e->getMessage(),
//                'user'    => $this->user(),
//                'toUrl'   => $tourl,
//                'seconds' => $seconds,
//            ) );
//            return $viewModel->setTemplate( 'wepo/partial/error.twig' );

        return $res;
    }

    public function pdfAction()
    {

        $data = $this->params()->fromRoute('data', 'dashboard');
        $view = $this->params()->fromRoute('view', 'list');
        $viewBox
              = $this->getViewBoxServiceVerify()->get(
            ucfirst($data) . '.' . $view
        );
        $viewBox->setParams($this->params());
        $viewBox->chooseStrategy('pdf');

        $viewBox->process();
        $res = $viewBox->output();

        return $res;
    }

    public function setupAction()
    {
        $viewBox
            = $this->getViewBoxServiceVerify()->get('Setup.index.list');
        $viewBox->setParams($this->params());
        $viewBox->chooseStrategy('html');
        $viewBox->process();
        $res = $viewBox->output();


        return $res;
    }

    public function fieldAction()
    {
        $viewBox = $this->getViewBoxServiceVerify()->get('Setup.field.list');
        $viewBox->chooseStrategy('html');
        $viewBox->setParams($this->params());
        $viewBox->process();
        $res = $viewBox->output();

        return $res;
    }

}
