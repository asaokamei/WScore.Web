<?php
namespace WScore\Web\Session;

class Storage implements StorageInterface
{
    /**
     * @Inject
     * @var \WScore\Web\Session\Session
     */
    public $manager;

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
     */
    public function __construct( $config=null )
    {
        $this->start();
        $this->setup( $config );
    }

    /**
     * @return SessionInterface
     */
    public function manager() {
        return $this->manager;
    }

    /**
     * @param string $config
     * @throws \RuntimeException
     * @return $this
     */
    public function setup( $config )
    {
        if( !$config ) {
            $this->data = & $this->manager->storage( $this->config );
        }
        elseif( is_string( $config ) ) {
            $this->data = & $this->manager->storage( $config );
        }
        elseif( is_array( $config ) ) {
            $this->data = & $config;
        }
        else {
            throw new \RuntimeException( '$config must be a string or an array. ' );
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function start() {
        return $this->manager->start();
    }

    /**
     * @return bool
     */
    public function isStarted() {
        return $this->manager->isStarted();
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