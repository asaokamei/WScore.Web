<?php
namespace WScore\tests\Auth;

require_once( __DIR__ . '/../../../autoload.php' );

use WScore\tests\Auth\Mocks\User;
use WScore\tests\Auth\Mocks\UserNotFound;
use \WScore\Web\Auth\AuthPost;

class AuthPostTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Mocks\User */
    public $user;

    /** @var  Mocks\UserNotFound */
    public $userNotFound;

    public function setUp()
    {
        $this->user = new User();
        $this->userNotFound = new UserNotFound();
    }

    function test1() 
    {
        $auth = new AuthPost();
        $this->assertEquals( 'WScore\Web\Auth\AuthPost', get_class( $auth ) );
    }
    
    function test_get_login_info_from_post()
    {
        $post = array(
            'action' => 'login',
            'loginUser' => 'test-user',
            'loginPass' => 'test-password',
        );
        $auth = new AuthPost( $post );
        $this->assertTrue( $auth->isPost() );
        $this->assertEquals( 'test-user', $auth->getLoginId() );
        $this->assertEquals( 'test-password', $auth->getLoginPw() );
    }

    function test_get_should_fail_if_no_login()
    {
        $post = array(
            'action' => 'not-login',
            'loginUser' => 'test-user',
            'loginPass' => 'test-password',
        );
        $auth = new AuthPost( $post );
        $this->assertFalse( $auth->isPost() );
        $this->assertEquals( null, $auth->getLoginId() );
        $this->assertEquals( null, $auth->getLoginPw() );
    }
    
    function test_verify_returns_loginID_on_success()
    {
        $post = array(
            'action' => 'login',
            'loginUser' => 'test',
            'loginPass' => 'test-password',
        );
        $auth = new AuthPost( $post );
        $this->assertTrue( $auth->isPost() );
        $this->assertEquals( 'test', $auth->verify( $this->user ) );
    }

    function test_verify_returns_null_if_not_login()
    {
        $post = array(
            'action' => 'no-login',
            'loginUser' => 'test',
            'loginPass' => 'test-password',
        );
        $auth = new AuthPost( $post );
        $this->assertFalse( $auth->isPost() );
        $this->assertEquals( null, $auth->verify( $this->user ) );
    }

    /**
     * @expectedException \WScore\Web\Auth\AuthPost_BadPW_Exception
     */
    function test_verify_throws_exception_if_wrong_password()
    {
        $post = array(
            'action' => 'login',
            'loginUser' => 'test',
            'loginPass' => 'bad-password',
        );
        $auth = new AuthPost( $post );
        $this->assertTrue( $auth->isPost() );
        $this->assertEquals( false, $auth->verify( $this->user ) );
    }

    /**
     * @expectedException \WScore\Web\Auth\AuthPost_NoID_Exception
     */
    function test_verify_throws_exception_if_no_userID()
    {
        $post = array(
            'action' => 'login',
            'loginPass' => 'bad-password',
        );
        $auth = new AuthPost( $post );
        $this->assertTrue( $auth->isPost() );
        $this->assertEquals( false, $auth->verify( $this->user ) );
    }

    /**
     * @expectedException \WScore\Web\Auth\AuthPost_NoPW_Exception
     */
    function test_verify_throws_exception_if_no_password()
    {
        $post = array(
            'action' => 'login',
            'loginUser' => 'test',
        );
        $auth = new AuthPost( $post );
        $this->assertTrue( $auth->isPost() );
        $this->assertEquals( false, $auth->verify( $this->user ) );
    }

    /**
     * @expectedException \WScore\Web\Auth\AuthPost_UserNotFound_Exception
     */
    function test_verify_throws_exception_if_no_such_user()
    {
        $post = array(
            'action' => 'login',
            'loginUser' => 'test',
            'loginPass' => 'test-password',
        );
        $auth = new AuthPost( $post );
        $this->assertTrue( $auth->isPost() );
        $this->assertEquals( false, $auth->verify( $this->userNotFound ) );
    }
}
