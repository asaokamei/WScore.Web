<?php
namespace WScore\tests;

require_once( __DIR__ . '/../../autoload.php' );

use WScore\Web\HttpResponder;

class HttpResponderTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \WScore\Web\HttpResponder */
    public $app;

    public $server = array(
        'REQUEST_METHOD' => 'GET',
        'SERVER_NAME' => 'test.wscore.jp',
        'REQUEST_URI' => '/WScore/test',
        'SCRIPT_NAME' => '/WScore/index.php',
    );

    public function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );
        $container->set( 'TemplateInterface', '\WScore\Template\PhpTemplate' );
        $this->container = $container;

        $this->app = $container->get( '\WScore\Web\HttpResponder' );
    }
    // +----------------------------------------------------------------------+

    function test1()
    {
        $this->assertEquals( 'WScore\Web\HttpResponder', get_class( $this->app ) );
    }

    function test_request_gets_info()
    {
        $app = $this->app;
        $app->setHttpRequest( $this->server );
        $this->assertEquals( '/WScore/', $app->request->baseURL );
        $this->assertEquals( 'test', $app->request->pathInfo );
        $this->assertEquals( '/WScore/', $app->request->appURL );
        $this->assertEquals( 'test', $app->request->appInfo );
        $this->assertEquals( 'get', $app->request->method );
    }
}
