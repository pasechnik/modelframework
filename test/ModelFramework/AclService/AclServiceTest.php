<?php

namespace ModelFramework\AclService;

use ModelFramework\DataModel\DataModel;
use Wepo\Controller\DashboardController;
use ModelFramework\Bootstrap;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use PHPUnit_Framework_TestCase;

class AclServiceTest extends PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;

    protected function setUp()
    {
        $serviceManager   = Bootstrap::getServiceManager();
        $this->controller = new DashboardController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array( 'controller' => 'index' ));
        $this->event      = new MvcEvent();
        $config           = $serviceManager->get('Config');
        $routerConfig     = isset($config[ 'router' ]) ? $config[ 'router' ] : array();
        $router           = HttpRouter::factory($routerConfig);
        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);

        $this->setTestUser();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function setTestUser()
    {
        if (!isset($this->controller)) {
            throw new \Exception('Controller is not set. Set controller before setting the User.');
        }
        $mainUser = $this->controller->Model('MainUser');
        $mainUser->exchangeArray(
            [
                "_id"         => new \MongoId("53a047288bc6c91f37603cdc"),
                "company_id"  => "533ec57a83971eba5c19bbb8",
                "role_id"     => new \MongoId("5295fdf7c5b9f222acd3c406"),
                "status"      => "normal",
                "status_id"   => new \MongoId("5295fdf7c5b9f222acd3c74c"),
                "created_dtm" => "2014-06-17 09:33:21",
                "login"       => "stas@acl.com",
                "password"    => "10a7cdd970fe135cf4f7bb55c0e3b59f",
                "fname"       => "Stanis",
                "lname"       => "Aclis",
            ]
        );
        $this->controller->mainUser($mainUser);

        return $this;
    }

    public function testIndexActionCanBeAccessed()
    {
        var_dump('testIndexAction');
        $this->routeMatch->setParam('action', 'index');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPushAndPop()
    {
        $stack = array();
        $this->assertEquals(0, count($stack));

        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertEquals(1, count($stack));

        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
    }

//    /*
// * @return DataModelInterface
// */
//    public function getUser()
//    {
//        $user = $this->getAuthServiceVerify()->getUser();
//        if ( $user == null )
//        {
//            throw new \Exception( ' the user does not set in AuthService' );
//
//        }
//
//        return $user;
//    }
//
//    /**
//     * @param string $modelName
//     *
//     * @return \ModelFramework\GatewayService\MongoGateway|null
//     * @throws \Exception
//     */
//    public function getGateway( $modelName )
//    {
//        $gateway = $this->getGatewayServiceVerify()->get( $modelName );
//        if ( $gateway == null )
//        {
//            throw new \Exception( $modelName . ' Gateway can not be created ' );
//        }
//
//        return $gateway;
//    }
//
//    /**
//     * @param $modelName
//     *
//     * @return DataModelInterface
//     * @throws \Exception
//     */
//    public function getAclData( $modelName )
//    {
//        $aclGateway = $this->getGateway( 'Acl' );
//        $user       = $this->getUser();
//        $acl        = $aclGateway->findOne( [ 'role_id' => $user->role_id, 'resource' => $modelName ] );
//        if ( $acl == null )
//        {
//            throw new \Exception( $modelName . ' Acl for role ' . $user->role . ' not found ' );
//        }
//
//        return $acl;
//    }
//
//    /**
//     * @param $modelName
//     *
//     * @return DataModelInterface
//     * @throws \Exception
//     */
//    public function get( $modelName )
//    {
//        return $this->getAclModel( $modelName );
//    }
//
//
//    /**
//     * @param $modelName
//     *
//     * @return DataModelInterface
//     * @throws \Exception
//     */
//    public function getAclModel( $modelName )
//    {
//        $aclData = new AclDataModel();
//
//        $dataModel = $this->getModelServiceVerify()->get( $modelName );
//        $aclData->setDataModel( $dataModel );
//
//        $aclModel = $this->getAclData( $modelName );
//        $aclData->setAclData( $aclModel );
//
//        return $aclData;
//    }
}
