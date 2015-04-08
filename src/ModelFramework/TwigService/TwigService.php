<?php

namespace ModelFramework\TwigService;

/**
 * Class TwigService
 * @package ModelFramework\TwigService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
class TwigService implements TwigServiceInterface
{
    private $service = null;

    public function __construct(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $this->service = $serviceManager;

    }

    /**
     * Parse twig template as string
     * @param $template
     * @param array $variables
     * @param array $params
     * @return string
     */
    public function getParseString($template, $variables = [], $params = [])
    {
        $twigRenderer = clone $this->service->get('zfctwigviewtwigrenderer');
        $loader = clone $this->service->get('twigenvironment');
        $loader->setLoader(clone $loader->getLoader());
        $loader->getLoader()->addLoader(new \Twig_Loader_String());
        return $loader->loadTemplate($template)
            ->render($variables);
    }
}
