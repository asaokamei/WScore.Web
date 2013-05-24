<?php
namespace WScore\tests\Auth;

require_once( __DIR__ . '/../../../autoload.php' );

use WScore\tests\Auth\Mocks\User;
use WScore\tests\Auth\Mocks\UserNotFound;
use WScore\Web\Auth\Auth;
use \WScore\Web\Auth\AuthPost;
use WScore\Web\Session;

class AuthTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \WScore\Web\Auth\Auth */
    public $auth;
    
    /** @var  Mocks\User */
    public $user;

    /** @var  Mocks\UserNotFound */
    public $userNotFound;

    /** @var  \WScore\Web\Auth\AuthPost */
    public $post_goodTest, $post_badPw, $post_noLogin;
    
    /** @var  \WScore\Web\Session */
    public $session_saved;
    
    public function setUp()
    {
        $this->user = new User();
        $this->userNotFound = new UserNotFound();

        $this->post_goodTest = new AuthPost( array(
            'action' => 'login',
            'loginUser' => 'test',
            'loginPass' => 'test-password',
        ) );

        $this->post_badPw = new AuthPost( array(
            'action' => 'login',
            'loginUser' => 'test',
            'loginPass' => 'bad-password',
        ) );

        $this->post_noLogin = new AuthPost( array(
            'action' => 'no-login',
            'loginUser' => 'test',
            'loginPass' => 'test-password',
        ) );
        
        $session_data = array();
        $this->auth = new Auth();
        $this->auth->session = new Session( $session_data, false );
        
        $session_data = array( $this->auth->auth_id => array(
            'user_id'     => 'test',
            'is_LoggedIn' => true,
            'login_time'  => new \DateTime(),
            'access_time' => new \DateTime(),
        ) );
        $this->session_saved = new Session( $session_data, false );

    }

    function test1()
    {
        $this->assertEquals( 'WScore\Web\Auth\Auth', get_class( $this->auth ) );
    }

    function test_good_login()
    {
        $auth = $this->auth;
        $auth->user = $this->user;
        $auth->post = $this->post_goodTest;
        $login = $auth->getAuth();
        
        // check login state
        $this->assertEquals( 'test', $login );
        $this->assertEquals( true, $auth->isLoggedIn() );
        $this->assertEquals( 'user-info', $auth->getUserInfo( 'info' ) );
        // check session
        $session_data = $auth->session->get( $auth->auth_id );
        $this->assertTrue( is_array( $session_data ) );
        $this->assertEquals( true, $session_data[ 'is_LoggedIn' ] );
        $this->assertEquals( 'test', $session_data[ 'user_id' ] );
        $this->assertEquals( 'DateTime', get_class( $session_data[ 'login_time' ] ) );
        $this->assertEquals( 'DateTime', get_class( $session_data[ 'access_time' ] ) );
    }

    function test_no_login()
    {
        $auth = $this->auth;
        $auth->user = $this->user;
        $auth->post = $this->post_noLogin;
        $login = $auth->getAuth();

        // check login state
        $this->assertEquals( null, $login );
        $this->assertEquals( false, $auth->isLoggedIn() );
        $this->assertEquals( null, $auth->getUserInfo( 'info' ) );
        // check session
        $session_data = $auth->session->get( $auth->auth_id );
        $this->assertEquals( null, $session_data );
    }

    /**
     * @expectedException \WScore\Web\Auth\AuthPost_BadPW_Exception
     */
    function test_bad_login_throws_an_exception()
    {
        $auth = $this->auth;
        $auth->user = $this->user;
        $auth->post = $this->post_badPw;
        // should throw an exception next. 
        $auth->getAuth();
        $this->assertTrue( false );
    }

    function test_session_login()
    {
        $auth = $this->auth;
        $auth->user = $this->user;
        $auth->post = $this->post_noLogin;
        $auth->session = $this->session_saved;
        $login = $auth->getAuth();

        // check login state
        $this->assertEquals( 'test', $login );
        $this->assertEquals( true, $auth->isLoggedIn() );
        $this->assertEquals( 'user-info', $auth->getUserInfo( 'info' ) );
        // check session
        $session_data = $auth->session->get( $auth->auth_id );
        $this->assertTrue( is_array( $session_data ) );
        $this->assertEquals( true, $session_data[ 'is_LoggedIn' ] );
        $this->assertEquals( 'test', $session_data[ 'user_id' ] );
        $this->assertEquals( 'DateTime', get_class( $session_data[ 'login_time' ] ) );
        $this->assertEquals( 'DateTime', get_class( $session_data[ 'access_time' ] ) );
    }
}