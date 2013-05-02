<?php
namespace WScore\Web\Auth;

/**
 * Class LoadPost
 *
 * @package WScore\Web\Auth
 * 
 * @cacheable
 */
class LoadPost
{
    /**
     * @var string
     */
    public $login_name = 'auth_name';

    /**
     * @var string
     */
    public $login_pass = 'auth_pass';

    /**
     * @var array
     */
    private $post = array();

    // +----------------------------------------------------------------------+
    /**
     *
     */
    public function __construct()
    {
    }
    
    /**
     * @param array $post
     * @return self
     */
    function with( $post )
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @param string $savedPass
     * @return bool
     */
    protected function matchPass( $savedPass )
    {
        $postPass = $this->getPass();
        if( $savedPass === $postPass ) return true;
        if( $savedPass === crypt( $postPass, $savedPass ) ) return true;
        if( $savedPass === md5( $postPass ) ) return true;
        return false;
    }

    /**
     * @return bool
     */
    public function getId() {
        return $this->getPost( $this->login_name, '[- _.@a-zA-Z0-9]' );
    }

    /**
     * @return bool
     */
    public function getPass() {
        return $this->getPost( $this->login_pass, '[- _.@a-zA-Z0-9]' );
    }

    /**
     * @param $name
     * @param $match
     * @return bool
     */
    private function getPost( $name, $match )
    {
        if( !array_key_exists( $name, $this->post ) ) return false;
        if( preg_match( '/^' . $match . '+$/i', $this->post[ $name ] ) ) {
            return $this->post[ $name ];
        }
        return false;
    }
}