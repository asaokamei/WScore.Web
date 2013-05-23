<?php
namespace WScore\Web\Auth;

class AuthPostException extends \RuntimeException {}

/**
 * Class AuthPost
 *
 * @package WScore\Web\Auth
 */
class AuthPost
{
    public $post_id = 'loginUser';
    public $post_pw = 'loginPass';
    public $action  = 'action';
    public $value   = 'login';

    public $data;

    public $id = null;
    public $pw = null;

    public function __construct( $data = array() )
    {
        if( !$data ) {
            $this->data = $_POST;
        } else {
            $this->data = $data;
        }
        if( $this->get( $this->action ) === $this->value ) {
            $this->id = $this->get( $this->post_id );
            $this->pw = $this->get( $this->post_pw );
        }
    }

    private function get( $name ) {
        return isset( $this->data[ $name ] ) ? $this->data[ $name ] : null;
    }

    public function getLoginId() {
        return $this->id;
    }

    public function getLoginPw() {
        return $this->pw;
    }

    public function isPost() {
        return (bool) $this->get( $this->action );
    }

    /**
     * @param UserInterface $user
     * @throws AuthPostException
     * @return string|null
     */
    public function verify( $user )
    {
        if( !$this->isPost() ) return false;

        if( !$id = $this->getLoginId() ) {
            throw new AuthPostException( 'no_id', 401 );
        }
        $user->setLoginId( $id );
        if( !$pw_jam = $user->getLoginPw( $id ) ) {
            throw new AuthPostException( 'no_user', 402 );
        };
        if( !$pw_raw = $this->getLoginPw() ) {
            throw new AuthPostException( 'no_pw', 403 );
        }
        if( $this->matchPassword( $pw_jam, $pw_raw ) ) {
            throw new AuthPostException( 'bad_pw', 404 );
        }
        return $id;
    }

    /**
     * @param string $pw_jam
     * @param string $pw_raw
     * @return bool
     */
    protected function matchPassword( $pw_jam, $pw_raw )
    {
        if( $pw_jam === crypt( $pw_raw, $pw_jam ) ) return true;
        return false;
    }
}

