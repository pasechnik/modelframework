<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 8/1/14
 * Time: 12:51 PM
 */

namespace ModelFramework\GatewayService;

interface GatewayAwareInterface
{
    /**
     * @param GatewayInterface $gateway
     *
     * @return $this
     */
    public function setGateway(GatewayInterface $gateway);

    /**
     * @return GatewayInterface
     */
    public function getGateway();

    /**
     * @return GatewayInterface
     * @throws \Exception
     */
    public function getGatewayVerify();
}
