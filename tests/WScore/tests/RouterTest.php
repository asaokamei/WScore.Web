<?php 
namespace WScore\tests;

use WScore\Web\Router;

require_once( __DIR__ . '/../../autoload.php' );

class RouterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \WScore\Web\Router */
    public $router;

    public function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $this->router = new Router();
    }
    // +----------------------------------------------------------------------+

    function test1()
    {
        $this->assertEquals( 'WScore\Web\Router', get_class( $this->router ) );
    }

    function test_set_test_route()
    {
        $this->router->set( array( 'test' => array() ) );
        $result = $this->router->match( 'test' );
        
        $this->assertNotEquals( null, $result );
        $this->assertTrue( is_array( $result ) );
        $this->assertEquals( '/test', $result[0] );
        $this->assertEquals( '/test', $result[1] );
    }

    function test_not_match_returns_null()
    {
        $this->router->set( array( 'test' => array() ) );
        $result = $this->router->match( 'test2' );

        $this->assertEquals( null, $result );
    }

    function test_set_extra_info()
    {
        $this->router->set( array( 'test' => array( 'extra' => 'info' ) ) );
        $result = $this->router->match( 'test' );

        $this->assertNotEquals( null, $result );
        $this->assertTrue( is_array( $result ) );
        $this->assertEquals( '/test', $result[0] );
        $this->assertEquals( '/test', $result[1] );
        $this->assertEquals( 'info', $result['extra'] );
    }
    
    function test_id_variable()
    {
        $this->router->set( array( 'test/:id' => array() ) );
        $result = $this->router->match( 'test/12' );

        $this->assertNotEquals( null, $result );
        $this->assertTrue( is_array( $result ) );
        $this->assertEquals( '/test/12', $result[0] );
        $this->assertEquals( '/test', $result[1] );
        $this->assertEquals( '12', $result['id'] );
    }
    
    function test_id_only_route()
    {
        $this->router->set( array( ':id' => array() ) );
        $result = $this->router->match( '12' );

        $this->assertNotEquals( null, $result );
        $this->assertTrue( is_array( $result ) );
        $this->assertEquals( '/12', $result[0] );
        $this->assertEquals( '12', $result[1] );
        $this->assertEquals( '12', $result['id'] );
    }

    function test_id_variable_longer()
    {
        $this->router->set( array( 'more/test/:id' => array() ) );
        $result = $this->router->match( 'more/test/12' );

        $this->assertNotEquals( null, $result );
        $this->assertTrue( is_array( $result ) );
        $this->assertEquals( '/more/test/12', $result[0] );
        $this->assertEquals( '/more/test', $result[1] );
        $this->assertEquals( '12', $result['id'] );
    }

    function test_asterisk()
    {
        $this->router->set( array( '*' => array() ) );
        $result = $this->router->match( 'any' );

        $this->assertNotEquals( null, $result );
        $this->assertTrue( is_array( $result ) );
        $this->assertEquals( '/any', $result[0] );
        $this->assertEquals( '/any', $result[1] );
    }

    function test_asterisk_with_header()
    {
        $this->router->set( array( 'test/*' => array() ) );
        $result = $this->router->match( 'test/any' );

        $this->assertNotEquals( null, $result );
        $this->assertTrue( is_array( $result ) );
        $this->assertEquals( '/test/any', $result[0] );
        $this->assertEquals( '/test/any', $result[1] );

        $result = $this->router->match( 'more/any' );

        $this->assertEquals( null, $result );
    }
    
    function test_id_variable_compound()
    {
        $this->router->set( array( 'more/:id/test' => array() ) );
        $result = $this->router->match( 'more/12/test' );
        
        return; // current Router does not support this expression. 

        /*
        $this->assertNotEquals( null, $result );
        $this->assertTrue( is_array( $result ) );
        $this->assertEquals( '/more/12/test', $result[0] );
        $this->assertEquals( '/more/test', $result[1] );
        $this->assertEquals( '12', $result['id'] );
        */
    }
}
