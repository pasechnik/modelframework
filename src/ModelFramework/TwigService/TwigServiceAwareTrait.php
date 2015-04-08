<?php
/**
 * Class TwigServiceAwareTrait
 * @package ModelFramework\TwigService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
namespace ModelFramework\TwigService;

trait TwigServiceAwareTrait{
    /**
     * @var TwigServiceInterface
     */
    private $_twigService = null;

    /**
     * @param TwigServiceInterface $twigService
     *
     * @return $this
     */
    public function setTwigService(TwigServiceInterface $twigService)
    {
        $this->_twigService = $twigService;
    }

    /**
     * @return TwigServiceInterface
     */
    public function getTwigService()
    {
        return $this->_twigService;
    }

    /**
     * @return TwigServiceInterface
     * @throws \Exception
     */
    public function getTwigServiceVerify()
    {
        $_twigService = $this->getTwigService();
        if ($_twigService == null || !$_twigService instanceof TwigServiceInterface) {
            throw new \Exception('TwigService does not set in the TwigServiceAware instance of '.
                get_class($this));
        }

        return $_twigService;
    }

}