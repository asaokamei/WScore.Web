<?php
namespace WScore\tests\Response;

require_once( __DIR__ . '/../../../autoload.php' );

class PageTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \WScore\Response\Request */
    public $request;

    /** @var \WScore\tests\Response\Mocks\PageMock */
    public $page;

    public function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );

        $this->request = $container->get( '\WScore\Response\Request' );
        $this->page    = $container->get( '\WScore\tests\Response\Mocks\PageMock' );
    }
    // +----------------------------------------------------------------------+

    function test0()
    {
        $this->assertEquals( 'WScore\Response\Request', get_class( $this->request ) );
        $this->assertEquals( 'WScore\tests\Response\Mocks\PageMock', get_class( $this->page ) );
    }

    function test_respond_invokes_onGet_method()
    {
        $this->page->setRequest( $this->request );
        $this->page->respond();
        $this->assertEquals( 'onGet', $this->page->onList[0] );
        $this->assertEquals( 'onGet was here.', $this->page->get( 'onGet' ) );
    }

    function test_respond_invokes_onTest_method()
    {
        $this->page->setRequest( $this->request->on( 'test' ) );
        $this->page->respond();
        $this->assertEquals( 'onTest', $this->page->onList[0] );
    }

    function test_options_returns_at_least_get_and_test()
    {
        $this->page->setRequest( $this->request->on( 'options' ) );
        $this->page->respond();
        $this->assertEquals( 'GET, TEST, RELOAD, ROOTS, OPTIONS', $this->page->headers[ 'ALLOW' ] );
    }

    function test_non_existing_method_returns_invalid_method()
    {
        $this->page->setRequest( $this->request->on( 'badRequest' ) );
        $this->page->respond();
        $this->assertEquals( '405', $this->page->statusCode );
    }

    function test_reload_fills_location_header()
    {
        $this->page->setRequest(
            $this->request->on( 'reload' )->uri( '/my/uri?test' )->path( '/path/to' )
        );
        $this->page->respond();
        $this->assertEquals( '302', $this->page->statusCode );
        $this->assertEquals( '/path/to/my/uri?test', $this->page->headers[ 'Location' ] );
    }

    function test_appRoot_fills_location_header()
    {
        $this->page->setRequest(
            $this->request->on( 'roots' )->uri( '/my/uri?test' )->path( '/path/to' )
        );
        $this->page->respond();
        $this->assertEquals( '302', $this->page->statusCode );
        $this->assertEquals( '/path/to', $this->page->headers[ 'Location' ] );
    }
}