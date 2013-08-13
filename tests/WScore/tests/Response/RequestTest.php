<?php
namespace WScore\tests\Response;

require_once( __DIR__ . '/../../../autoload.php' );

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \WScore\Response\Request */
    public $request;
    public function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );
        $container->set( 'TemplateInterface', '\WScore\Template\PhpTemplate' );

        $this->request = $container->get( '\WScore\Response\Request' );
    }
    // +----------------------------------------------------------------------+

    function test0()
    {
        $this->assertEquals( 'WScore\Response\Request', get_class( $this->request ) );
    }
    
    function test_uri()
    {
        $uri = '/path/to/test';
        $this->request->uri( $uri );
        $this->assertEquals( $uri, $this->request->getInfo( 'requestUri' ) );
    }
    
    function test_match()
    {
        $uri = '/path/to/test';
        $this->request->uri( $uri );
        $this->assertTrue( $this->request->match( '/pa') );
        $this->assertTrue( $this->request->match( '/path') );
        $this->assertTrue( $this->request->match( '/path/') );
        $this->assertTrue( $this->request->match( '/path/to') );
        $this->assertTrue( $this->request->match( '/path/to/') );
        $this->assertFalse( $this->request->match( '/badPath') );
        $this->assertFalse( $this->request->match( 'path') );
        $this->assertFalse( $this->request->match( '/path/not/') );
    }
    
    function test_modifyUri()
    {
        $uri = '/path/to/test';
        $this->request->uri( $uri );
        $this->assertEquals( $uri, $this->request->getInfo( 'requestUri' ) );
        $this->assertEquals( '',   $this->request->getInfo( 'requestRoot' ) );
        
        $this->request->modifyUri( '/path/' );
        $this->assertEquals( 'to/test', $this->request->getInfo( 'requestUri' ) );
        $this->assertEquals( '/path/',  $this->request->getInfo( 'requestRoot' ) );
    }

    function test_modifyUri_with_Root()
    {
        $uri  = '/path/to/test';
        $root = '/root/is';
        $this->request->uri( $uri );
        $this->request->path( $root );
        $this->assertEquals( $uri, $this->request->getInfo( 'requestUri' ) );
        $this->assertEquals( $root,   $this->request->getInfo( 'requestRoot' ) );

        $this->request->modifyUri( '/path/' );
        $this->assertEquals( 'to/test', $this->request->getInfo( 'requestUri' ) );
        $this->assertEquals( '/root/is/path/',  $this->request->getInfo( 'requestRoot' ) );
    }

    function test_copy()
    {
        $uri = '/path/to/test';
        $this->request->uri( $uri );
        $request = $this->request->copy( '/path/' );
        $this->assertEquals( 'to/test', $request->getInfo( 'requestUri' ) );
        $this->assertEquals( '/path/',  $request->getInfo( 'requestRoot' ) );
    }

    function test_copy_with_root()
    {
        $uri  = '/path/to/test';
        $root = '/root/is';
        $this->request->uri( $uri );
        $this->request->path( $root );
        $this->assertEquals( $uri, $this->request->getInfo( 'requestUri' ) );
        $this->assertEquals( $root,   $this->request->getInfo( 'requestRoot' ) );
        
        $request = $this->request->copy( '/path/' );
        $this->assertEquals( 'to/test', $request->getInfo( 'requestUri' ) );
        $this->assertEquals( '/root/is/path/',  $request->getInfo( 'requestRoot' ) );
    }
}