<?php
namespace WScore\tests\Http;

use WScore\Web\Http\Request;

require_once( __DIR__ . '/../../../autoload.php' );

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Web\Http\Request */
    public $request;

    public $server = array(
        'REQUEST_METHOD' => 'GET',
        'SERVER_NAME' => 'test.wscore.jp',
        'REQUEST_URI' => '/WScore/test',
        'SCRIPT_NAME' => '/WScore/index.php',
    );
    
    public function setUp()
    {
        $this->request = new Request();
    }
    // +----------------------------------------------------------------------+

    function test1()
    {
        $this->assertEquals( 'WScore\Web\Http\Request', get_class( $this->request ) );
    }
    
    function test_basic_uri()
    {
        $this->request->setServer( $this->server );

        $this->assertEquals( '/WScore/test', $this->request->getRequestUri() );
        $this->assertEquals( '/WScore/', $this->request->getBaseUrl() );
        $this->assertEquals( 'test', $this->request->getPathInfo() );
        $this->assertEquals( 'get', $this->request->getMethod() );
    }
    
    function test_post_method()
    {
        $this->server[ 'REQUEST_METHOD' ] = 'POST';
        $this->request->setServer( $this->server );

        $this->assertEquals( '/WScore/test', $this->request->getRequestUri() );
        $this->assertEquals( '/WScore/', $this->request->getBaseUrl() );
        $this->assertEquals( 'test', $this->request->getPathInfo() );
        $this->assertEquals( 'post', $this->request->getMethod() );
    }

    function test_post_method_override()
    {
        $this->server[ 'REQUEST_METHOD' ] = 'POST';
        $data = array( '_method' => 'testMethod' );
        $this->request->setServer( $this->server );
        $this->request->setPost( $data );

        $this->assertEquals( '/WScore/test', $this->request->getRequestUri() );
        $this->assertEquals( '/WScore/', $this->request->getBaseUrl() );
        $this->assertEquals( 'test', $this->request->getPathInfo() );
        $this->assertEquals( 'testMethod', $this->request->getMethod() );
    }

    function test_getInfo()
    {
        $this->request->setServer( $this->server );
        $info = $this->request->getInfo();
        
        $this->assertEquals( '/WScore/test', $info[ 'requestURI' ] );
        $this->assertEquals( '/WScore/', $info[ 'baseURL' ] );
        $this->assertEquals( 'test', $info[ 'pathInfo' ] );
        $this->assertEquals( 'get', $info[ 'method' ] );
    }
}
