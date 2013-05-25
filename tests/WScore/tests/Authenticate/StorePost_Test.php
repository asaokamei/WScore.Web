<?php
namespace WScore\tests\Authenticate;

use WScore\Web\Authenticate\Authenticate;
use WScore\Web\Authenticate\StorePost;

require_once( __DIR__ . '/../../../autoload.php' );

class StorePost_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Container  */
    public $container;

    /** @var  \WScore\Web\Authenticate\StorePost */
    public $post;

    public function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );
        class_exists( 'WScore\Web\Authenticate\Authenticate' );
        $this->post = $container->get( 'WScore\Web\Authenticate\StorePost' );
        $this->post->data = array(
            'action'    => 'login',
            'loginUser' => 'test',
            'loginPass' => 'test-password',
        );
    }

    function test1()
    {
        $this->assertEquals( 'WScore\Web\Authenticate\StorePost', get_class( $this->post ) );
    }

    function test_login()
    {
        $post = $this->post;
        $info = $post->loadLogin();
        $this->assertTrue( is_array( $info ) );
        $this->assertEquals( 'test', $info[ 'user_id' ] );
        $this->assertEquals( 'test-password', $info[ 'password' ] );
    }

    function test_no_login_return_null()
    {
        $post = $this->post;
        $post->data[ 'action' ] = 'no-login';
        $info = $post->loadLogin();
        $this->assertTrue( is_null( $info ) );
    }

    /**
     * @expectedException \WScore\Web\Authenticate\Auth_NoID_Exception
     */
    function test_verify_throws_exception_if_no_userID()
    {
        $post = $this->post;
        unset( $post->data[ 'loginUser' ] );
        $post->loadLogin();
    }

    /**
     * @expectedException \WScore\Web\Authenticate\Auth_NoPW_Exception
     */
    function test_verify_throws_exception_if_no_password()
    {
        $post = $this->post;
        unset( $post->data[ 'loginPass' ] );
        $info = $post->loadLogin();
    }
    
    function test_logout() {
        $this->post->logout();
    }
    
    function test_saveLogin() {
        $this->post->saveLogin( array() );
    }
}
