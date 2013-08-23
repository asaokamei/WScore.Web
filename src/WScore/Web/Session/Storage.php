<?php
namespace WScore\Web\Session;

class Storage implements StorageInterface, \Serializable
{
    /**
     * @Inject
     * @var \WScore\Web\Session\Session
     */
    public $session;

    /**
     * config is a string in $_SESSION.
     * or, config is set to null if an array is given.
     *
     * @var string
     */
    protected $config = '..Session.';

    /**
     * @var array
     */
    protected $data = array();

    // +----------------------------------------------------------------------+
    /**
     * @param null $config
     * @return \WScore\Web\Session\Storage
     */
    public function __construct( $config=null )
    {
        $this->start();
        $this->setup( $config );
    }

    /**
     * @return SessionInterface
     */
    public function session() {
        return $this->session;
    }

    /**
     * @param string $config
     * @return $this
     */
    public function setup( $config )
    {
        if( is_array( $config ) ) {
            $this->data = & $config;
            $this->config = null;
            return $this;
        }
        if( is_string( $config ) ) {
            $this->config = $config;
        }
        return $this->connectSession();
    }

    /**
     * @return $this
     */
    public function connectSession()
    {
        if( !isset( $this->session->storage[ $this->config ] ) ) {
            $this->session->storage[ $this->config ] = array();
        }
        $this->data = & $this->session->storage[ $this->config ];
        return $this;

    }

    /**
     * @return bool
     */
    public function start() {
        return $this->session->start();
    }

    /**
     * @return bool
     */
    public function isStarted() {
        return $this->session->isStarted();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has( $name ) {
        return array_key_exists( $name, $this->data );
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get( $name, $default=null )
    {
        if( $this->has( $name ) ) return $this->data[ $name ];
        return $default;
    }

    /**
     * @param string $name
     * @param $value
     * @return $this
     */
    public function set( $name, $value )
    {
        $this->data[ $name ] = $value;
        return $this;
    }

    /**
     * @return array Attributes
     */
    public function all() {
        return $this->data;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function remove( $name )
    {
        if( $this->has( $name ) ) unset( $this->data[ $name ] );
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->data = array();
        return $this;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize( get_object_vars( $this ) );
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized   The string representation of the object.
     * @return void
     */
    public function unserialize( $serialized )
    {
        $info = unserialize( $serialized );
        foreach( $info as $key => $val ) {
            $this->$key = $val;
        }
        $this->connectSession();
    }
}