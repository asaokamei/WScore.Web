<?php
namespace WScore\tests\Response;

require_once( __DIR__ . '/../../../autoload.php' );

class DispatchTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \WScore\Response\Request */
    public $request;

    /** @var \WScore\tests\Response\Mocks\DispatchMe */
    public $dispatch;

    public function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );

        $this->request = $container->get( '\WScore\Response\Request' );
        $this->dispatch    = $container->get( '\WScore\tests\Response\Mocks\DispatchMe' );
    }
    // +----------------------------------------------------------------------+

    function test0()
    {
        $this->assertEquals( 'WScore\Response\Request', get_class( $this->request ) );
        $this->assertEquals( 'WScore\tests\Response\Mocks\DispatchMe', get_class( $this->dispatch ) );
    }

    function test_dispatch_view_only_file()
    {
        $this->dispatch->setRequest( $this->request->uri( 'ViewOnly' ) );
        /** @var $response \WScore\Response\Response */
        $response = $this->dispatch->respond();

        $this->assertEquals( 'WScore\Response\Response', get_class( $response ) );
        $this->assertEquals( __DIR__ . '/Mocks/View/ViewOnly.php', $response->template );
    }

    function test_dispatch_page_response()
    {
        $this->dispatch->setRequest( $this->request->uri( 'PageMe' ) );
        /** @var $response \WScore\Response\Response */
        $response = $this->dispatch->respond();

        $this->assertEquals( 'WScore\tests\Response\Mocks\Page\PageMe', get_class( $response ) );
        $this->assertEquals( 'PageMe was here.', $response->get( 'onGet' ) );
        $this->assertEquals( null, $response->template );
    }

    function test_dispatch_page_and_set_template()
    {
        $this->dispatch->setRequest( $this->request->uri( 'PageMe' )->on( 'view' ) );
        /** @var $response \WScore\Response\Response */
        $response = $this->dispatch->respond();

        $this->assertEquals( 'WScore\tests\Response\Mocks\Page\PageMe', get_class( $response ) );
        $this->assertEquals( 'PageMe was here.', $response->get( 'onView' ) );
        $this->assertEquals( __DIR__ . '/Mocks/View/ViewOnly.php', $response->template );
    }
}