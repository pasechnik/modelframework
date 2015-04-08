<?php

/**
 * Class ViewBoxService
 *
 * @package ModelFramework\ViewBoxService
 */

namespace ModelFramework\ViewBoxService;

use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\PDFService\PDFServiceAwareInterface;
use ModelFramework\PDFService\PDFServiceAwareTrait;
use ModelFramework\ViewBoxService\ViewBox\ViewBox;
use ModelFramework\ViewBoxService\ViewBoxConfig\ViewBoxConfig;
use ModelFramework\ViewService\ViewServiceAwareInterface;
use ModelFramework\ViewService\ViewServiceAwareTrait;
use ModelFramework\TwigService\TwigServiceAwareInterface;
use ModelFramework\TwigService\TwigServiceAwareTrait;


class ViewBoxService
    implements ViewBoxServiceInterface, ConfigServiceAwareInterface,
               ViewServiceAwareInterface, AuthServiceAwareInterface,
               PDFServiceAwareInterface, TwigServiceAwareInterface
{

    use ViewServiceAwareTrait, ConfigServiceAwareTrait, AuthServiceAwareTrait, PDFServiceAwareTrait, TwigServiceAwareTrait;

    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function getViewBox($viewBoxName)
    {
        return $this->createViewBox($viewBoxName);
    }

    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function get($viewBoxName)
    {
        return $this->getViewBox($viewBoxName);
    }

    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function createViewBox($viewBoxName)
    {
        // this object will deal with all view of model stuff
        $viewBox = new ViewBox();

        $viewBoxConfig = $this->getConfigServiceVerify()
            ->getByObject($viewBoxName, new ViewBoxConfig());

        if ($viewBoxConfig == null) {
            throw new \Exception('Please fill ViewBoxConfig for the '
                . $viewBoxName . '. I can\'t work on');
        }
        $viewBox->setViewBoxConfig($viewBoxConfig);

        $viewBox->setViewService($this->getViewServiceVerify());
        $viewBox->setAuthService($this->getAuthServiceVerify());
        $viewBox->setPDFService($this->getPDFServiceVerify());
        $viewBox->setTwigService($this->getTwigServiceVerify());

        return $viewBox;
    }
}
