<?php
/**
 * Class TwigServiceAwareInterface
 * @package ModelFramework\TwigService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
namespace ModelFramework\TwigService;

interface TwigServiceAwareInterface
{
    /**
     * @param TwigServiceInterface $twigService
     *
     * @return $this
     */
    public function setTwigService(TwigServiceInterface $twigService);

    /**
     * @return TwigServiceInterface
     */
    public function getTwigService();

    /**
     * @return TwigServiceInterface
     * @throws \Exception
     */
    public function getTwigServiceVerify();
}
