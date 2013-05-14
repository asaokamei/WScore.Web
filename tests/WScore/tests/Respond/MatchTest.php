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
        $container->set( 'TemplateInterface', '\WScore\Template\PhpTemplate' );
        
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
        $this->assertFalse( isset( $response->data[ 'id' ] ) );
    }

    function test_matched_onGet_with_id()
    {
        $request = $this->request->set( '/matched/101' );
        /** @var $response \WScore\tests\Respond\test\Page\Matched */
        $response = $this->matcher->request( $request )->respond();
        $this->assertEquals( 'WScore\tests\Respond\test\Page\Matched', get_class( $response ) );
        $this->assertEquals( 'get',     $response->data[ 'method' ] );
        $this->assertEquals( 'Matched', $response->data[ 'I-am' ] );
        $this->assertEquals( '101', $response->data[ 'id' ] );
    }

    function test_match_using_render()
    {
        $request = $this->request->set( '/other' );
        /** @var $response \WScore\tests\Respond\test\Page\Matched */
        $response = $this->matcher->request( $request )->respond();
        $this->assertEquals( 'WScore\tests\Respond\test\Page\Matched', get_class( $response ) );
        $this->assertEquals( 'get',     $response->data[ 'method' ] );
        $this->assertEquals( 'Matched', $response->data[ 'I-am' ] );
    }


    function test_match_for_view_only()
    {
        $request = $this->request->set( '/ViewOnly' );
        /** @var $response \WScore\tests\Respond\test\Page\Matched */
        $response = $this->matcher->request( $request )->respond();
        $response->render();
        $this->assertEquals( 'This is: ViewOnly', $response->content );
    }
    
    function test_matched_onGet_with_php()
    {
        $request = $this->request->set( '/matched.php' );
        $this->matcher->setRoute( array( '*' => array() ) );
        /** @var $response \WScore\tests\Respond\test\Page\Matched */
        $response = $this->matcher->request( $request )->respond();
        $this->assertEquals( 'WScore\tests\Respond\test\Page\Matched', get_class( $response ) );
        $this->assertEquals( 'get',     $response->data[ 'method' ] );
        $this->assertEquals( 'Matched', $response->data[ 'I-am' ] );
    }

}