<?php
namespace ModelFramework\ViewBoxService\ViewBox\OutputStrategy;

/**
 * Interface OutputStrategyAwareInterface
 * @package ModelFramework\ViewBoxService\Output\Strategy
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
interface OutputStrategyAwareInterface
{
    /**
     * @param ModelServiceInterface $modelService
     *
     * @return $this
     */
    public function setModelService(ModelServiceInterface $modelService);

    /**
     * @return ModelServiceInterface
     */
    public function getModelService();

    /**
     * @return ModelServiceInterface
     * @throws \Exception
     */
    public function getModelServiceVerify();
}
