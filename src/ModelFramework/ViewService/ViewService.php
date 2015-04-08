<?php

/**
 * Class ViewService
 *
 * @package ModelFramework\ViewService
 */

namespace ModelFramework\ViewService;

use ModelFramework\AclService\AclServiceAwareInterface;
use ModelFramework\AclService\AclServiceAwareTrait;
use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\FileService\FileServiceAwareInterface;
use ModelFramework\FileService\FileServiceAwareTrait;
use ModelFramework\FilesystemService\FilesystemServiceAwareInterface;
use ModelFramework\FilesystemService\FilesystemServiceAwareTrait;
use ModelFramework\ListParamsService\ListParamsServiceAwareInterface;
use ModelFramework\ListParamsService\ListParamsServiceAwareTrait;
use ModelFramework\PDFService\PDFServiceAwareInterface;
use ModelFramework\PDFService\PDFServiceAwareTrait;
use ModelFramework\FormService\FormServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\LogicService\LogicServiceAwareInterface;
use ModelFramework\LogicService\LogicServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\FormService\FormServiceAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use ModelFramework\ViewService\ViewConfig\ViewConfig;
use ModelFramework\TwigService\TwigServiceAwareInterface;
use ModelFramework\TwigService\TwigServiceAwareTrait;

class ViewService
    implements ViewServiceInterface, ConfigServiceAwareInterface,
               GatewayServiceAwareInterface, AclServiceAwareInterface,
               ModelServiceAwareInterface,
               FormServiceAwareInterface, AuthServiceAwareInterface,
               LogicServiceAwareInterface, PDFServiceAwareInterface,
               QueryServiceAwareInterface, FileServiceAwareInterface,
                FilesystemServiceAwareInterface,TwigServiceAwareInterface, ListParamsServiceAwareInterface
{

    use ConfigServiceAwareTrait, GatewayServiceAwareTrait, AclServiceAwareTrait,
        ModelServiceAwareTrait, FormServiceAwareTrait, AuthServiceAwareTrait, PDFServiceAwareTrait,
        LogicServiceAwareTrait, QueryServiceAwareTrait, FileServiceAwareTrait, FilesystemServiceAwareTrait, TwigServiceAwareTrait, ListParamsServiceAwareTrait;

    /**
     * @param string $viewName
     *
     * @return View|ViewInterface
     * @throws \Exception
     */
    public function get($viewName)
    {
        return $this->getView($viewName);
    }

    /**
     * @param string $viewName
     *
     * @return View|ViewInterface
     * @throws \Exception
     */
    public function getView($viewName)
    {
        return $this->createView($viewName);
    }

    /**
     * @param string $viewName
     *
     * @return View|ViewInterface
     * @throws \Exception
     */
    protected function createView($viewName)
    {
        $view = new View();
        $view->setName($viewName);
        $view->setAuthService($this->getAuthServiceVerify());
        $view->setAclService($this->getAclServiceVerify());
        $view->setLogicService($this->getLogicServiceVerify());
        $view->setConfigService($this->getConfigServiceVerify());
        $viewConfig = $this->getConfigServiceVerify()
            ->getByObject($view->getName(), new ViewConfig());
        if ($viewConfig == null) {
            throw new \Exception('Please fill ViewConfig for the ' .
                $view->getName() . '. I can\'t work on');
        }
        $view->setViewConfig($viewConfig);
        $view->setModelService($this->getModelServiceVerify());
        // info about model - how it is organized. it will be useful
        $parsedModelConfig = $this->getModelServiceVerify()
            ->getParsedModelConfig($viewConfig->model);
        $view->setParsedModelConfig($parsedModelConfig);
        // model view should deal with acl enabled model
        $aclModel
            = $this->getAclServiceVerify()->getAclDataModel($viewConfig->model);
        // primary gateway for data ops
        $gateway = $this->getGatewayServiceVerify()
            ->get($viewConfig->model, $aclModel);
        $view->setGateway($gateway);
        $view->setGatewayService($this->getGatewayServiceVerify());
        $view->setFormService($this->getFormServiceVerify());
        $view->setConfigService($this->getConfigServiceVerify());
        $view->setQueryService($this->getQueryServiceVerify());
        $view->setFileService($this->getFileServiceVerify());
        $view->setFilesystemService($this->getFilesystemServiceVerify());
        $view->setPDFService($this->getPDFServiceVerify());
        $view->setTwigService($this->getTwigServiceVerify());
        $view->setListParamsService($this->getListParamsServiceVerify());
        $view->init();

        return $view;
    }
}
