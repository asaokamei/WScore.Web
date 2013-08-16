<?php
namespace WScore\Web\Session;

class Storage implements StorageInterface
{
    /**
     * @Inject
     * @var \WScore\Web\Session\Session
     */
    public $session;

    /**
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
     * @throws \RuntimeException
     * @return $this
     */
    public function setup( $config )
    {
        if( is_array( $config ) ) {
            $this->data = & $config;
            return $this;
        }
        elseif( !$config ) {
            $config = $this->config;
        }
        elseif( !is_string( $config ) ) {
            throw new \RuntimeException( '$config must be a string or an array. ' );
        }
        if( !isset( $this->session->storage[ $config ] ) ) {
            $this->session->storage[ $config ] = array();
        }
        $this->data = & $this->session->storage[ $config ];
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
}