<?php
namespace WScore\tests\Respond;

require_once( __DIR__ . '/../../../autoload.php' );

class MatchTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \WScore\Web\Request */
    public $request;
    
    /** @var \WScore\tests\Respond\test\DispatchTester */
    public $matcher;

    public function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );
        
        $this->request = $container->get( '\WScore\Web\Request' );
        $this->matcher = $container->get( '\WScore\tests\Respond\test\DispatchTester' );
    }
    // +----------------------------------------------------------------------+

    function test_no_match_returns_null()
    {
        $request = $this->request->set( '/no_match' );
        $response = $this->matcher->request( $request )->respond();
        $this->assertNull( $response );
    }
    
    function test_matched_onGet()
    {
        $request = $this->request->set( '/matched' );
        /** @var $response \WScore\tests\Respond\test\Page\Matched */
        $response = $this->matcher->request( $request )->respond();
        $this->assertEquals( 'WScore\tests\Respond\test\Page\Matched', get_class( $response ) );
        $this->assertEquals( 'get',     $response->data[ 'method' ] );
        $this->assertEquals( 'Matched', $response->data[ 'I-am' ] );
    }

    function test_match_onBadMethod_returns_status_405()
    {
        $request = $this->request->set( '/matched' )->on( 'BadMethod');
        /** @var $response \WScore\tests\Respond\test\Page\Matched */
        $response = $this->matcher->request( $request )->respond();
        $this->assertEquals( 'WScore\tests\Respond\test\Page\Matched', get_class( $response ) );
        $this->assertEquals( '405',     $response->statusCode );
    }

}