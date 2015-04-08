<?php
/**
 * Class ViewBox
 *
 * @package ModelFramework\ViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewBoxService\ViewBox;

use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\PDFService\PDFServiceAwareInterface;
use ModelFramework\PDFService\PDFServiceAwareTrait;
use ModelFramework\Utility\Arr;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;
use ModelFramework\ViewBoxService\ResponseAwareInterface;
use ModelFramework\ViewBoxService\ResponseAwareTrait;
use ModelFramework\ViewBoxService\ViewBoxConfig\ViewBoxConfigAwareInterface;
use ModelFramework\ViewBoxService\ViewBoxConfig\ViewBoxConfigAwareTrait;
use ModelFramework\ViewService\ViewServiceAwareInterface;
use ModelFramework\ViewService\ViewServiceAwareTrait;
use ModelFramework\ViewBoxService\ViewBox\OutputStrategy\OutputStrategyInterface;
use ModelFramework\ViewBoxService\ViewBox\OutputStrategy\OutputStrategyAwareTrait;
use Zend\View\Model\ViewModel as ZendViewModel;
use ModelFramework\TwigService\TwigServiceAwareInterface;
use ModelFramework\TwigService\TwigServiceAwareTrait;

class ViewBox implements ViewBoxInterface, ViewBoxConfigAwareInterface, ParamsAwareInterface,
                         ViewServiceAwareInterface, AuthServiceAwareInterface,
                         ResponseAwareInterface 

{

    use ViewBoxConfigAwareTrait, ParamsAwareTrait, ViewServiceAwareTrait, AuthServiceAwareTrait, ResponseAwareTrait, PDFServiceAwareTrait, OutputStrategyAwareTrait, TwigServiceAwareTrait;

    private $_data = [];
    private $_redirect = null;

    public function setRedirect(ZendViewModel $redirect)
    {
        $this->_redirect = $redirect;
    }

    public function getRedirect()
    {
        return $this->_redirect;
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

    public function clearData()
    {
        $this->_data = [];
    }

    public function setDataFields()
    {
        $viewBoxConfig      = $this->getViewBoxConfigVerify();
        $result             = [];
        $result['data']     = [];
        $result['document'] = $viewBoxConfig->document;
        $result['blocks']   = $viewBoxConfig->blocks;
        $result['template'] = $viewBoxConfig->template;
        $result['title']    = $viewBoxConfig->title;
        $result['mode']     = $viewBoxConfig->mode;
        $result['user']     = $this->getAuthServiceVerify()->getUser();
        $this->setData($result);
    }

    /**
     * @return $this|void
     * @throws \Exception
     */
    public function process()
    {

        $this->setDataFields();
        $params = [];
        foreach (
            $this->getViewBoxConfigVerify()->blocks as $blockName =>
            $viewNames
        ) {
            foreach ($viewNames as $viewName) {
                $modelView = $this->getViewServiceVerify()->get($viewName);
                $modelView->setParams($this->getParamsVerify());
                $modelView->process();
                if ( !$modelView->isAllowed()) {
                    continue;
                }
                if ($modelView->hasRedirect()) {
                    $this->setRedirect($modelView->getRedirect());

                    return;
                }
                if ($modelView->hasResponse()) {
                    $this->setResponse($modelView->getResponse());

                    return;
                }
                $data    = $modelView->getData();
                $vParams = Arr::getDoubtField($data, 'params', []);
                if (count($vParams)) {
                    $params = Arr::merge($params, $vParams);
                }
                $viewResults
                    = ['data' => [$blockName => [$viewName => $modelView->getData()]]];
                $this->setData($viewResults);
            }
        }
        $params['data']
                        = strtolower($this->getViewBoxConfigVerify()->document);
        $params['view'] = strtolower($this->getViewBoxConfigVerify()->mode);
        $this->setData([
            'viewboxparams' => $params,
            'user'          => $this->getAuthServiceVerify()
                ->getUser()
        ]);

        return $this;
    }

    /**
     * @return ZendViewModel
     */
    public function output()
    {
        if ($this->hasRedirect()) {
            return $this->getRedirect();
        }
        if ($this->hasResponse()) {
            return $this->getResponse();
        }

        $this->getStrategy()->setViewBox($this);
        return $this->getStrategy()->output();

    }

    public function outputPDF()
    {

        if ($this->hasRedirect()) {
            return $this->getRedirect();
        }
        if ($this->hasResponse()) {
            return $this->getResponse();
        }
        $data = $this->getData();

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename=file.pdf');
        header('Content-Transfer-Encoding: binary');
        //  header('Content-Length: 55766');
        header('Accept-Ranges: bytes');

        $pdf = $this->getPDFServiceVerify();
        echo $pdf->getPDFtoSave($data['template'], $data);
        exit;
    }

}
