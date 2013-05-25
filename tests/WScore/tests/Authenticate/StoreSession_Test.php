<?php
namespace WScore\tests\Authenticate;

use WScore\Web\Authenticate\Authenticate;
use WScore\Web\Authenticate\StorePost;
use WScore\Web\Session;

require_once( __DIR__ . '/../../../autoload.php' );

class StoreSession_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Container  */
    public $container;

    /** @var \WScore\Web\Session  */
    public $session;
    
    /** @var \WScore\Web\Authenticate\StoreSession */
    public $store;

    public function setUp()
    {
        class_exists( 'WScore\Web\Authenticate\Authenticate' );
        
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );

        $this->session = new Session( null, false );
        $container->set( '\WScore\Web\Session', $this->session );
        $this->store = $container->get( 'WScore\Web\Authenticate\StoreSession' );
    }

    function test1()
    {
        $this->assertEquals( 'WScore\Web\Authenticate\StoreSession', get_class( $this->store ) );
    }
    
    function test_login()
    {
        $this->session->set( 'Auth.ID', array(
            'test' => 'login'
        ) );
        $info = $this->store->loadLogin();
        $this->assertTrue( is_array( $info ) );
        $this->assertEquals( 'login', $info[ 'test' ] );
    }
    
    function test_saveLogin()
    {
        $info = array(
            'test' => 'save',
        );
        $this->store->saveLogin( $info );
        $saved = $this->session->get( 'Auth.ID' );
        $this->assertTrue( is_array( $saved ) );
        $this->assertEquals( 'save', $saved[ 'test' ] );
        $this->assertEquals( 'DateTime', get_class( $saved[ 'access_time' ] ) );
    }
    
    function test_logout()
    {
        $this->session->set( 'Auth.ID', array(
            'test' => 'login'
        ) );
        $info = $this->store->loadLogin();
        $this->assertTrue( is_array( $info ) );
        
        $this->store->logout();
        
        $saved = $this->session->get( 'Auth.ID' );
        $this->assertNull( $saved );
    }
}