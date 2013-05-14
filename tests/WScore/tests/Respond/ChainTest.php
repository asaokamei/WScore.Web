<?php
namespace WScore\tests\Respond;

require_once( __DIR__ . '/../../../autoload.php' );

class ChainTest extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Container  */
    public $container;
    
    /** @var  \WScore\Web\Request */
    public $request;

    /** @var \WScore\Web\Respond\Chain */
    public $chain;
    
    /** @var  \WScore\tests\Respond\test\Responder */
    public $res, $resAdmin, $resMain, $resAfter, $resDone;

    public function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );
        $container->set( 'TemplateInterface', '\WScore\Template\PhpTemplate' );
        $this->container = $container;

        $this->request = $container->get( '\WScore\Web\Request' );
        $this->chain   = $container->get( '\WScore\Web\Respond\Chain' );

        $this->res = $this->container->get( '\WScore\tests\Respond\test\Responder' );
        $this->res->name = null;
        $this->resAdmin = clone( $this->res );        $this->resAdmin->name = 'admin';
        $this->resMain  = clone( $this->res );        $this->resMain ->name = 'main';
        $this->resAfter = clone( $this->res );        $this->resAfter->name = 'after';
        $this->resDone  = clone( $this->res );        $this->resDone ->name = null;

        $chain = $this->chain;
        $chain->addResponder( $this->res );
        $chain->addResponder( $this->resAdmin, 'admin/' );
        $chain->addResponder( $this->resMain,  'main/' );
        $chain->addResponder( $this->resAfter );
        $chain->addResponder( $this->resDone, true );
    }
    // +----------------------------------------------------------------------+
    
    function test1()
    {
        $this->assertEquals( 'WScore\Web\Respond\Chain', get_class( $this->chain ) );
    }
    
    function test_chain_for_root()
    {
        $this->request->set( '/' );
        $response = $this->chain->request( $this->request )->respond();
        $this->assertEquals( 'after', $response );
        $this->assertTrue(  $this->res->responded );
        $this->assertFalse( $this->resAdmin->responded );
        $this->assertFalse( $this->resMain->responded );
        $this->assertTrue(  $this->resAfter->responded );
        $this->assertTrue(  $this->resDone->responded );
    }

    function test_chain_for_admin()
    {
        $this->request->set( 'admin/' );
        $response = $this->chain->request( $this->request )->respond();
        $this->assertEquals( 'admin', $response );
        $this->assertTrue(  $this->res->responded );
        $this->assertTrue(  $this->resAdmin->responded );
        $this->assertFalse( $this->resMain->responded );
        $this->assertFalse( $this->resAfter->responded );
        $this->assertTrue(  $this->resDone->responded );

        $this->request->set( 'admin/some' );
        $response = $this->chain->request( $this->request )->respond();
        $this->assertEquals( 'admin', $response );
        $this->assertTrue(  $this->res->responded );
        $this->assertTrue(  $this->resAdmin->responded );
        $this->assertFalse( $this->resMain->responded );
        $this->assertFalse( $this->resAfter->responded );
        $this->assertTrue(  $this->resDone->responded );
    }

    function test_chain_for_main()
    {
        $this->request->set( 'main/' );
        $response = $this->chain->request( $this->request )->respond();
        $this->assertEquals( 'main', $response );
        $this->assertTrue(  $this->res->responded );
        $this->assertFalse( $this->resAdmin->responded );
        $this->assertTrue(  $this->resMain->responded );
        $this->assertFalse( $this->resAfter->responded );
        $this->assertTrue(  $this->resDone->responded );

        $this->request->set( 'main/more' );
        $response = $this->chain->request( $this->request )->respond();
        $this->assertEquals( 'main', $response );
        $this->assertTrue(  $this->res->responded );
        $this->assertFalse( $this->resAdmin->responded );
        $this->assertTrue(  $this->resMain->responded );
        $this->assertFalse( $this->resAfter->responded );
        $this->assertTrue(  $this->resDone->responded );
    }
}