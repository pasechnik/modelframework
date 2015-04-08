<?php

namespace ModelFramework\Session\SaveHandler;

use ModelFramework\GatewayService\GatewayAwareInterface;
use ModelFramework\GatewayService\GatewayAwareTrait;
use Zend\Session\SaveHandler\SaveHandlerInterface;

/**
 * MongoGateway session store
 *
 * @package    Zend Framework 2
 * @since      PHP >=5.3.xx
 * @author     Artem Bondarenko a.bondarenko@cronagency.com
 * @author     Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @filesource ModelFramework/Session/SaveHandler/MongoGateway
 */
class MongoGateway
    implements SaveHandlerInterface, GatewayAwareInterface
{

    use GatewayAwareTrait;

    /**
     * @access protected
     * @var string $sessionName
     */
    protected $sessionName;

    /**
     * @access protected $lifetime
     * @var int
     */
    protected $lifetime = null;

    /**
     * Open session
     *
     * @param string $savePath
     * @param string $name
     *
     * @return bool
     */
    public function open($savePath, $name)
    {
        // Note: session save path is not used
        $this->sessionName = $name;
        if (null === $this->lifetime) {
            $this->lifetime = ini_get('session.gc_maxlifetime');
        }

        return true;
    }


    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }


    /**
     * Close session
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session from DB
     *
     * @param int $id session id
     *
     * @return string
     */
    public function read($id)
    {
        $session = $this->getGatewayVerify()->findOne([
            'session_id' => $id,
            'title'      => $this->sessionName,
        ]);

        if (null !== $session) {
            if ($session->modified + $session->lifetime > time()) {
                return $session->data;
            }
            $this->destroy($id);
        }

        return '';
    }

    /**
     * Save session to DB
     *
     * @param int    $id session id
     * @param string $data
     */
    public function write($id, $data)
    {
        $criteria = [
            'session_id' => $id,
            'title'      => $this->sessionName,
        ];

        $session = $this->getGatewayVerify()->findOne($criteria);

        if (null === $session) {
            $session = $this->getGatewayVerify()->model();
        }

        $session->session_id = $id;
        $session->title      = $this->sessionName;
        $session->modified   = time();
        $session->lifetime   = $this->lifetime;
        $session->data       = $data;

        $this->getGatewayVerify()->save($session);

    }

    /**
     * Drop session
     *
     * @param int $id session id
     *
     * @return bool
     */
    public function destroy($id)
    {
        $this->getGatewayVerify()->delete(
            ['session_id' => $id, 'title' => $this->sessionName]
        );
    }

    /**
     * Garbage Collector
     *
     * @param int $maxLifetime
     *
     * @return bool
     */
    public function gc($maxLifetime)
    {
        $result = $this->getGatewayVerify()->delete([
            'modified' => ['$lt' => time() - $maxLifetime]
        ]);

        return (bool)(isset($result['ok']) ? $result['ok'] : $result);
    }
}
