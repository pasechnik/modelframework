<?php
/**
 * Class Query
 * @package ModelFramework\QueryService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService;

use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\QueryService\Observer\AbstractObserver;
use ModelFramework\QueryService\QueryConfig\QueryConfigAwareInterface;
use ModelFramework\QueryService\QueryConfig\QueryConfigAwareTrait;
use ModelFramework\Utility\Arr;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;

class Query
    implements QueryInterface, QueryConfigAwareInterface, \SplSubject,
               ParamsAwareInterface, AuthServiceAwareInterface
{

    use QueryConfigAwareTrait, ParamsAwareTrait, AuthServiceAwareTrait;

    private $_data = [ ];
    private $_where = [ ];
    private $_order = [ ];

    protected $allowed_observers = [
        'RouteParamObserver',
        'LetterParamObserver',
        'LikeObserver',
        'StaticObserver',
        'SearchObserver',
        'OrderObserver',
        'PermissionObserver',
        'AclObserver',
        'CurrentUserObserver',
        'FormatObserver',
    ];

    protected $observers = [ ];

    public function attach( \SplObserver $observer )
    {
        $this->observers[ ] = $observer;
    }

    public function detach( \SplObserver $observer )
    {
        $key = array_search( $observer, $this->observers );
        if ($key) {
            unset( $this->observers[ $key ] );
        }
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update( $this );
        }
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setData( array $data )
    {
        $this->_data = Arr::merge( $this->_data, $data );
//        $this->_data += $data;
    }

    protected function clearData()
    {
        $this->_data = [ ];
    }

    public function swipeDataKey( $key )
    {
        $ar = Arr::getDoubtField( $this->getData(), $key, [ ] );
        unset( $this->_data[ $key ] );

        return $ar;
    }

    public function setWhere( array $where )
    {
        $this->_where = Arr::merge( $this->_where, $where );

        return $this;
    }

    public function getWhere()
    {
        return $this->_where;
    }

    public function setOrder( array $order )
    {
        $this->_order = Arr::merge( $this->_order, $order );

        return $this;
    }

    public function getOrder()
    {
        return $this->_order;
    }

    public function getFields()
    {
        return $this->getQueryConfigVerify()->fields;
    }

    public function getModelName()
    {
        return $this->getQueryConfigVerify()->model;
    }

    public function getFormat( $field )
    {
        $data = $this->getData();
        if (isset( $data[ 'format' ][ $field ] )) {
            return $data[ 'format' ][ $field ];
        }

        return;
    }

    public function init()
    {
        foreach ($this->getQueryConfigVerify()->observers as $observer =>
                 $obConfig) {
            if (is_numeric( $observer )) {
                $observer = $obConfig;
                $obConfig = null;
            }
            if (!in_array( $observer, $this->allowed_observers )) {
                throw new \Exception( $observer . ' is not allowed in ' .
                                      get_class( $this ) );
            }
            $observerClassName =
                'ModelFramework\QueryService\Observer\\' . $observer;
            /**
             * @var AbstractObserver $_obs
             */
            $_obs = new $observerClassName();
            if (!empty( $obConfig )) {
                $_obs->setRootConfig( $obConfig );
            }
            $this->attach( $_obs );
        }
    }

    public function process()
    {
        $this->notify();

        return $this;
    }

    public function model()
    {
    }
}
