<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 8/1/14
 * Time: 12:53 PM
 */

namespace ModelFramework\GatewayService;

trait GatewayAwareTrait
{
    /**
     * @var GatewayInterface
     */
    private $_gateway = null;

    /**
     * @param GatewayInterface $gateway
     *
     * @return $this
     */
    public function setGateway(GatewayInterface $gateway)
    {
        $this->_gateway = $gateway;

        return $this;
    }

    /**
     * @return GatewayInterface
     */
    public function getGateway()
    {
        return $this->_gateway;
    }

    /**
     * @return GatewayInterface
     * @throws \Exception
     */
    public function getGatewayVerify()
    {
        $gateway = $this->getGateway();
        if ($gateway == null || !$gateway instanceof GatewayInterface) {
            throw new \Exception('Gateway does not set in the GatewayAware instance of '.get_class($this));
        }

        return $gateway;
    }
}
