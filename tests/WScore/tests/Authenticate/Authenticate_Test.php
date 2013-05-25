<?php
namespace WScore\tests\Authenticate;

use WScore\Web\Authenticate\Authenticate;
use WScore\Web\Authenticate\AuthInterface;
use WScore\Web\Authenticate\StorePost;
use WScore\Web\Session;

require_once( __DIR__ . '/../../../autoload.php' );

class Authenticate_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\DiContainer\Container  */
    public $container;

    /** @var \WScore\Web\Session  */
    public $session_http;

    /** @var \WScore\Web\Authenticate\StoreSession */
    public $session;
    
    /** @var \WScore\Web\Authenticate\StorePost  */
    public $post;
    
    /** @var \WScore\Web\Authenticate\Authenticate  */
    public $auth;

    /** @var  array */
    public $post_goodTest, $post_badPw, $post_noLogin;

    /** @var  \WScore\Web\Authenticate\AuthUserInterface */
    public $user_good, $user_none;
    
    public function setUp()
    {
        class_exists( 'WScore\Web\Authenticate\Authenticate' );

        /** @var $container \WScore\DiContainer\Container */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );

        $this->session_http = new Session( null, false );
        $container->set( '\WScore\Web\Session', $this->session_http );
        
        $this->session = $container->get( 'WScore\Web\Authenticate\StoreSession' );
        $container->set( '\WScore\Web\Authenticate\StoreSession', $this->session );
        
        $this->post = new StorePost();
        $container->set( '\WScore\Web\Authenticate\StorePost', $this->post );
        
        $this->auth = $container->get( '\WScore\Web\Authenticate\Authenticate' );

        // make various post data. 
        $this->post_goodTest = array(
            'action'    => 'login',
            'loginUser' => 'test',
            'loginPass' => 'test-password',
        );

        $this->post_noLogin = array(
            'action'    => 'no-login',
            'loginUser' => 'test',
            'loginPass' => 'test-password',
        );

        $this->post_badPw = array(
            'action'    => 'login',
            'loginUser' => 'test',
            'loginPass' => 'bad-password',
        );

        // make various user model
        $this->user_good = $container->get( '\WScore\tests\Authenticate\Mocks\User' );
        $this->user_none = $container->get( '\WScore\tests\Authenticate\Mocks\UserNotFound' );
    }

    function test1()
    {
        $this->assertEquals( 'WScore\Web\Authenticate\Authenticate', get_class( $this->auth ) );
    }

    function test_good_login()
    {
        $auth = $this->auth;
        $auth->post->data = $this->post_goodTest;
        $auth->user = $this->user_good;
        $login = $auth->getAuth();

        $this->assertTrue( $login );
        $this->assertTrue( $auth->isLoggedIn() );
        $this->assertEquals( 'test', $auth->getUserId() );
        $this->assertEquals( 'DateTime', get_class( $auth->getLastAccess() ) );
        $this->assertEquals( 'DateTime', get_class( $auth->get( AuthInterface::LOGIN_TIME ) ) );
        $this->assertEquals( AuthInterface::BY_POST_FORM, $auth->get( AuthInterface::LOGIN_METHOD ) );
        $this->assertEquals( AuthInterface::LOGIN_VALID, $auth->get( AuthInterface::IS_LOGIN ) );
    }


    function test_no_login()
    {
        $auth = $this->auth;
        $auth->post->data = $this->post_noLogin;
        $auth->user = $this->user_good;
        $login = $auth->getAuth();

        $this->assertFalse( $login );
        $this->assertFalse( $auth->isLoggedIn() );
        $this->assertEquals( null, $auth->getUserId() );
        $this->assertEquals( null, $auth->getLastAccess() );
    }

    /**
     * @expectedException \WScore\Web\Authenticate\Auth_PasswordNotMatch_Exception
     */
    function test_bad_password_throws_exception()
    {
        $auth = $this->auth;
        $auth->post->data = $this->post_badPw;
        $auth->user = $this->user_good;
        $login = $auth->getAuth();
    }


    function test_login_by_session()
    {
        $auth = $this->auth;
        $auth->post->data = $this->post_noLogin;
        $auth->session->session->set( 'Auth.ID', array(
            AuthInterface::USER_ID => 'test',
            AuthInterface::IS_LOGIN => AuthInterface::LOGIN_VALID,
            AuthInterface::ACCESS_TIME => 'test-access-time',
            AuthInterface::LOGIN_METHOD => 'By-Test',
            AuthInterface::LOGIN_TIME => new \DateTime(),
            'test' => 'test',
        ) );
        $auth->user = $this->user_good;
        $login = $auth->getAuth();

        $this->assertTrue( $login );
        $this->assertTrue( $auth->isLoggedIn() );
        $this->assertEquals( 'test', $auth->getUserId() );
        $this->assertEquals( 'test-access-time', $auth->getLastAccess() );
        $this->assertEquals( 'DateTime', get_class( $auth->get( AuthInterface::LOGIN_TIME ) ) );
        $this->assertEquals( 'By-Test', $auth->get( AuthInterface::LOGIN_METHOD ) );
        $this->assertEquals( AuthInterface::LOGIN_VALID, $auth->get( AuthInterface::IS_LOGIN ) );
    }

    function test_login_by_code()
    {
        $auth = $this->auth;
        $auth->post->data = $this->post_noLogin;
        $auth->user = $this->user_good;
        $auth->login( 'test' );

        $this->assertTrue( $auth->isLoggedIn() );
        $this->assertEquals( 'test', $auth->getUserId() );
        $this->assertEquals( 'DateTime', get_class( $auth->getLastAccess() ) );
        $this->assertEquals( 'DateTime', get_class( $auth->get( AuthInterface::LOGIN_TIME ) ) );
        $this->assertEquals( 'by-login', $auth->get( AuthInterface::LOGIN_METHOD ) );
        $this->assertEquals( AuthInterface::LOGIN_VALID, $auth->get( AuthInterface::IS_LOGIN ) );
    }

    function test_logout_from_session()
    {
        $auth = $this->auth;
        $auth->post->data = $this->post_noLogin;
        $auth->session->session->set( 'Auth.ID', array(
            AuthInterface::USER_ID => 'test',
            AuthInterface::IS_LOGIN => AuthInterface::LOGIN_VALID,
            AuthInterface::ACCESS_TIME => 'test-access-time',
            AuthInterface::LOGIN_METHOD => 'By-Test',
            AuthInterface::LOGIN_TIME => new \DateTime(),
            'test' => 'test',
        ) );
        $auth->user = $this->user_good;
        $login = $auth->getAuth();
        $this->assertTrue( $login );
        
        $auth->logout();
        $this->assertFalse( $auth->isLoggedIn() );
        $this->assertEquals( null, $auth->getUserId() );
        $this->assertEquals( 'test-access-time', $auth->getLastAccess() );
        $this->assertEquals( null, $auth->get( AuthInterface::LOGIN_TIME ) );
        $this->assertEquals( null, $auth->get( AuthInterface::LOGIN_METHOD ) );
        $this->assertEquals( null, $auth->get( AuthInterface::IS_LOGIN ) );
    }

}