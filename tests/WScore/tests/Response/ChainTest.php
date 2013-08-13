<?php
namespace WScore\tests\Response;

require_once( __DIR__ . '/../../../autoload.php' );

class ChainTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \WScore\Response\Request */
    public $request;

    /** @var \WScore\tests\Response\Mocks\ChainMe */
    public $chain;

    /** @var  \WScore\tests\Response\Mocks\Responsibility */
    public $res;

    public function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );

        $this->request = $container->get( '\WScore\Response\Request' );
        $this->chain    = $container->get( '\WScore\tests\Response\Mocks\ChainMe' );

        $this->res    = $container->get(  '\WScore\tests\Response\Mocks\Responsibility' );
    }
    // +----------------------------------------------------------------------+

    function test0()
    {
        $this->assertEquals( 'WScore\Response\Request', get_class( $this->request ) );
        $this->assertEquals( 'WScore\tests\Response\Mocks\ChainMe', get_class( $this->chain ) );
        $this->assertEquals( 'WScore\tests\Response\Mocks\Responsibility', get_class( $this->res ) );
    }

    function test_chain_basic()
    {
        $res1 = clone( $this->res );
        $res2 = clone( $this->res );
        $res3 = clone( $this->res );
        $this->chain->addModule( $res1 );
        $this->chain->addModule( $res2 );
        $this->chain->addModule( $res3 );
        $response = $this->chain->respond();

        $this->assertEquals( '', $response );
        $this->assertTrue( $res1->responded );
        $this->assertTrue( $res2->responded );
        $this->assertTrue( $res3->responded );
    }

    function test_chain_with_uri()
    {
        $res1 = clone( $this->res );
        $res2 = clone( $this->res );
        $res3 = clone( $this->res );
        $this->chain->addModule( $res1, '/not' );
        $this->chain->addModule( $res2, '/path' );
        $this->chain->addModule( $res3 );
        $response = $this->chain->setRequest( $this->request->uri( '/path' ) )->respond();

        $this->assertEquals( '', $response );
        $this->assertFalse( $res1->responded );
        $this->assertTrue( $res2->responded );
        $this->assertTrue( $res3->responded );
    }

    function test_chain_ignores_if_response_is_returned()
    {
        $res1 = clone( $this->res );
        $res2 = clone( $this->res );
        $res2->name = 'response#2';
        $res3 = clone( $this->res );
        $this->chain->addModule( $res1 );
        $this->chain->addModule( $res2 );
        $this->chain->addModule( $res3 );
        $response = $this->chain->respond();

        $this->assertEquals( 'response#2', $response );
        $this->assertTrue( $res1->responded );
        $this->assertTrue( $res2->responded );
        $this->assertFalse( $res3->responded );
    }
}