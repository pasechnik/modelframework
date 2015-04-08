<?php

namespace ModelFramework\GatewayService;

interface GatewayServiceAwareInterface
{
    /**
     * @param GatewayServiceInterface $gatewayService
     *
     * @return $this
     */
    public function setGatewayService(GatewayServiceInterface $gatewayService);

    /**
     * @return GatewayServiceInterface
     */
    public function getGatewayService();

    /**
     * @return GatewayServiceInterface
     * @throws \Exception
     */
    public function getGatewayServiceVerify();
}
