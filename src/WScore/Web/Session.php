<?php
namespace WScore\Web;

/**
 * Session wrapper.
 */

class Session
{
    /**  name of session variable */
    const SESSION_ID  = '_session_id_';

    /**  name of tokens stored in Session.     */
    const  TOKEN_ID   = 'session..token.ids';

    /**  name of token send via post */
    const  TOKEN_NAME = 'sessionTokenValue_';

    /** @var bool  flag to check if session started. */
    protected $session_start = false;

    /** @var array|bool   where session data is */
    protected $_session = null;

    /** @var null   temporary saves generated token. */
    protected $session_token = null;

    // +-------------------------------------------------------------+
    /**
     * @param null|string|array $config
     * @param bool $start
     */
    function __construct( $config=null, $start=true )
    {
        $this->start( $start );
        $this->config( $config );
    }

    /**
     * configures which data storage to use. 
     * uses $storage if an array, uses $_SESSION[ $storage ] if string is given as $storage. 
     * 
     * @param null|string|array $storage
     * @return Session
     */
    public function config( $storage=null )
    {
        if( is_array( $storage ) ) {
            $this->_session = $storage;
        }
        elseif( is_string( $storage ) ) {
            if( !isset( $_SESSION[ $storage ] ) ) $_SESSION[ $storage ] = array();
            $this->_session = &$_SESSION[ $storage ];
        }
        else {
            if( !isset( $_SESSION[ self::SESSION_ID ] ) ) $_SESSION[ self::SESSION_ID ] = array();
            $this->_session = &$_SESSION[ self::SESSION_ID ];
        }
        return $this;
    }

    /**
     * @param bool $start
     * @return bool
     */
    public function start( $start=true )
    {
        if( !$start ) {
            $this->session_start = true;
        }
        elseif( !$this->session_start ) {
            ob_start();
            session_start();
            $this->session_start = true;
        }
        return true;
    }
    // +-------------------------------------------------------------+
    //  set/get/del variables to Session. 
    // +-------------------------------------------------------------+
    /**
     * @param $name
     * @param $value
     * @return bool
     */
    public function set( $name, $value )
    {
        $this->_session[ $name ] = $value;
        return $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function del( $name )
    {
        if( array_key_exists( $name,  $this->_session ) ) {
            unset( $this->_session[ $name ] );
        }
        return true;
    }

    /**
     * @param $name
     * @return bool
     */
    public function get( $name )
    {
        if( array_key_exists( $name,  $this->_session ) ) {
            return $this->_session[ $name ];
        }
        return false;
    }
    public function pop( $name )
    {
        $val = $this->get( $name );
        $this->del( $name );
        return $val;
    }
    // +-------------------------------------------------------------+
    //  managing token for CSRF.
    // +-------------------------------------------------------------+
    /**
     * @return string
     */
    public function pushToken()
    {
        $token = md5( 'session.dumb' . time() . mt_rand(1,100*100) . __DIR__ );
        $this->_pushToken( $token );
        $this->session_token = $token;
        return $token;
    }

    /**
     * @param $token
     */
    protected function _pushToken( $token )
    {
        static::start();
        if( !isset( $this->_session[ static::TOKEN_ID ] ) ) {
            $this->_session[ static::TOKEN_ID ] = array();
        }
        $max_token = 20;
        $this->_session[ static::TOKEN_ID ][] = $token;
        if( count( $this->_session[ static::TOKEN_ID ] ) > $max_token ) {
            $num_remove = count( $this->_session[ static::TOKEN_ID ] ) - $max_token;
            $this->_session[ static::TOKEN_ID ] =
                array_slice( $this->_session[ static::TOKEN_ID ], $num_remove );
        }
    }

    public function popTokenTagName() {
        return static::TOKEN_NAME;
    }
    /**
     * @return string
     */
    public function popToken() {
        return $this->session_token;
    }
    
    /**
     * @return string
     */
    public function popTokenTag()
    {
        $name  = static::TOKEN_NAME;
        $value = $this->session_token;
        return "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\">";
    }

    /**
     * @return bool
     */
    public function verifyToken()
    {
        if( !isset( $_POST[ static::TOKEN_NAME ] ) ) return false;
        if( empty( $this->_session[ static::TOKEN_ID ] ) ) return false;
        $token = $_POST[ static::TOKEN_NAME ];
        if( in_array( $token, $this->_session[ static::TOKEN_ID ] ) ) {
            foreach( $this->_session[ static::TOKEN_ID ] as $k=>$v ) {
                if( $v === $token ) {
                    unset( $this->_session[ static::TOKEN_ID ][$k] );
                }
            }
            $this->_session[ static::TOKEN_ID ] = array_values( $this->_session[ static::TOKEN_ID ] );
            return true;
        }
        return false;
    }
    // +-------------------------------------------------------------+
}