<?php
namespace WScore\Web\Auth;

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
}

