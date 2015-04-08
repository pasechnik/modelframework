<?php

namespace ModelFramework\GatewayService;

trait GatewayServiceAwareTrait
{
    /**
     * @var GatewayServiceInterface
     */
    private $_gatewayService = null;

    /**
     * @param GatewayServiceInterface $gatewayService
     *
     * @return $this
     */
    public function setGatewayService(GatewayServiceInterface $gatewayService)
    {
        $this->_gatewayService = $gatewayService;

        return $this;
    }

    /**
     * @return GatewayServiceInterface
     */
    public function getGatewayService()
    {
        return $this->_gatewayService;
    }

    /**
     * @return GatewayServiceInterface
     * @throws \Exception
     */
    public function getGatewayServiceVerify()
    {
        $_gatewayService = $this->getGatewayService();
        if ($_gatewayService == null || !$_gatewayService instanceof GatewayServiceInterface) {
            throw new \Exception('GatewayService does not set in the GatewayServiceAware instance of '.get_class($this));
        }

        return $_gatewayService;
    }
}
